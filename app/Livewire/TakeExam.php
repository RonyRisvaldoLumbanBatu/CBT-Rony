<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Result;
use App\Services\ExamGrader;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TakeExam extends Component
{
    // Toleransi keterlambatan submit (detik) untuk latensi jaringan
    private const SUBMIT_GRACE_SECONDS = 30;

    // Batas pelanggaran (keluar tab/fullscreen) sebelum ujian disubmit paksa
    public const MAX_STRIKES = 3;

    public Exam $exam;

    public $currentQuestionIndex = 0;

    public $answers = [];

    public $timeLeft;

    // Variabel antarmuka untuk PIN
    public $isPinVerified = false;

    public $isFullscreen = false;

    public $inputPin = '';

    // Variabel baru untuk menampung soal yang sudah diacak
    public $questionsData = [];

    // Menyimpan daftar ID soal yang ditandai ragu-ragu
    public $doubtfulQuestions = [];

    // Jumlah pelanggaran, sumber kebenarannya di SESSION (bukan di browser),
    // supaya refresh halaman tidak me-reset hitungan
    public $cheatStrikes = 0;

    public function verifyPin()
    {
        // Rate limit: maksimal 5 percobaan per menit per siswa per ujian
        // (PIN 6 karakter bisa di-brute-force tanpa ini)
        $rateKey = 'verify-pin:'.auth()->id().':'.$this->exam->id;

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $detik = RateLimiter::availableIn($rateKey);
            session()->flash('pin_error', "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik.");

            return;
        }

        if (strtoupper($this->inputPin) === strtoupper($this->exam->token)) {
            RateLimiter::clear($rateKey);
            $this->isPinVerified = true;
            session()->put('pin_verified_'.$this->exam->id, true);
        } else {
            RateLimiter::hit($rateKey, 60);
            session()->flash('pin_error', 'PIN ujian tidak valid atau salah!');
        }
    }

    public function mount($id)
    {
        // 1. GATEKEEPER
        $sudahUjian = Result::where('user_id', auth()->id())
            ->where('exam_id', $id)
            ->exists();

        if ($sudahUjian) {
            session()->flash('error', 'Kamu sudah mengerjakan ujian ini. Tidak bisa mengulang!');

            return redirect()->route('dashboard');
        }

        // 2. Tarik data ujian
        $this->exam = Exam::with('questions.options')->findOrFail($id);

        // Tolak akses langsung via URL ke ujian yang belum diaktifkan guru
        if (! $this->exam->is_active) {
            session()->flash('error', 'Ujian ini belum dibuka. Hubungi '.strtolower(term('guru')).'mu!');

            return redirect()->route('dashboard');
        }

        // Tolak siswa dari kelas lain (ujian ber-target kelas tertentu)
        if (! $this->exam->isOpenFor(auth()->user())) {
            session()->flash('error', 'Ujian ini bukan untuk '.strtolower(term('kelas')).'mu.');

            return redirect()->route('dashboard');
        }

        // Ujian tanpa soal tidak bisa dikerjakan (view mengasumsikan minimal 1 soal)
        if ($this->exam->questions->isEmpty()) {
            session()->flash('error', 'Ujian ini belum memiliki soal. Hubungi '.strtolower(term('guru')).'mu!');

            return redirect()->route('dashboard');
        }

        // Verifikasi PIN
        if (empty($this->exam->token)) {
            $this->isPinVerified = true; // Jika guru belum set PIN, langsung loncat
        } else {
            $this->isPinVerified = session()->get('pin_verified_'.$id, false);
        }

        // 3. MESIN PENGURUTAN TIPE & PENGACAK SOAL (ANTI-NYONTEK) & SESSION RESTORE
        $sessionKeyQuestions = 'ujian_questions_'.$this->exam->id;
        $sessionKeyAnswers = 'ujian_answers_'.$this->exam->id;
        $sessionKeyDoubtful = 'ujian_doubtful_'.$this->exam->id;
        $sessionKeyIndex = 'ujian_index_'.$this->exam->id;

        if (session()->has($sessionKeyQuestions)) {
            // Restore jika siswa ter-refresh/mati lampu
            $this->questionsData = session()->get($sessionKeyQuestions);
            $this->answers = session()->get($sessionKeyAnswers, []);
            $this->doubtfulQuestions = session()->get($sessionKeyDoubtful, []);
            $this->currentQuestionIndex = session()->get($sessionKeyIndex, 0);
        } else {
            $acakSoal = [];

            // Urutan tipe soal yang diinginkan: PG -> PG Kompleks -> Benar/Salah -> Isian -> Essay
            $typeOrder = ['pg' => 1, 'pg_kompleks' => 2, 'benar_salah' => 3, 'isian' => 4, 'essay' => 5];

            // Acak semua soal dulu, lalu di-sort berdasarkan tipe
            $sortedQuestions = $this->exam->questions->shuffle()->sortBy(function ($q) use ($typeOrder) {
                return $typeOrder[$q->type] ?? 99;
            });

            foreach ($sortedQuestions as $soal) {
                $options = [];
                if (in_array($soal->type, ['pg', 'pg_kompleks'])) {
                    $options = $soal->options->shuffle()->toArray();
                } elseif ($soal->type === 'benar_salah') {
                    $options = $soal->options->toArray();
                }

                $acakSoal[] = [
                    'id' => $soal->id,
                    'question_text' => $soal->question_text,
                    'type' => $soal->type,
                    'image_path' => $soal->image_path,
                    'youtube_url' => $soal->youtube_url,
                    'options' => $options,
                ];

                // Cegah bug Livewire: Checkbox butuh inisialisasi awal Array kosong []
                if ($soal->type === 'pg_kompleks') {
                    $this->answers[$soal->id] = [];
                }
            }
            $this->questionsData = $acakSoal;

            // Simpan state awal ke session untuk ketahanan (Disaster Recovery)
            session()->put($sessionKeyQuestions, $this->questionsData);
            session()->put($sessionKeyAnswers, $this->answers);
            session()->put($sessionKeyDoubtful, $this->doubtfulQuestions);
            session()->put($sessionKeyIndex, $this->currentQuestionIndex);
        }

        // Restore hitungan pelanggaran (refresh halaman tidak me-reset strike)
        $this->cheatStrikes = session()->get('ujian_strikes_'.$this->exam->id, 0);

        // 4. TIMER ANTI-CHEAT MENGGUNAKAN SESSION
        $kunciSession = 'ujian_selesai_'.$this->exam->id;

        if (session()->has($kunciSession)) {
            $waktuSelesai = session()->get($kunciSession);
            $this->timeLeft = max(0, $waktuSelesai - now()->timestamp);
        } else {
            $this->timeLeft = $this->exam->time_limit * 60;
            session()->put($kunciSession, now()->timestamp + $this->timeLeft);
        }
    }

    public function decrementTimer()
    {
        if ($this->timeLeft > 0) {
            $this->timeLeft--;
        } else {
            $this->submitExam();
        }
    }

    // Auto-Save setiap ada perubahan jawaban
    public function updated($property)
    {
        if (str_starts_with($property, 'answers')) {
            $this->updatedAnswers();
        }
    }

    public function updatedAnswers()
    {
        // Setelah batas waktu lewat, perubahan jawaban tidak disimpan lagi
        // (mencegah siswa membekukan timer di browser lalu terus mengerjakan)
        if ($this->isTimeUp()) {
            return;
        }

        session()->put('ujian_answers_'.$this->exam->id, $this->answers);
    }

    private function isTimeUp(): bool
    {
        $deadline = session()->get('ujian_selesai_'.$this->exam->id);

        return $deadline !== null
            && now()->timestamp > $deadline + self::SUBMIT_GRACE_SECONDS;
    }

    public function nextQuestion()
    {
        // Sesuaikan validasi jumlah soal dengan array baru
        if ($this->currentQuestionIndex < count($this->questionsData) - 1) {

            // Auto-save: penting dipanggil setiap ganti halaman soal
            $this->updatedAnswers();

            $this->currentQuestionIndex++;
            session()->put('ujian_index_'.$this->exam->id, $this->currentQuestionIndex);

            event(new \App\Events\StudentExamUpdate(
                $this->exam->id,
                auth()->user()->name,
                'Sedang mengerjakan Soal '.($this->currentQuestionIndex + 1)
            ));
        }
    }

    public function prevQuestion()
    {
        if ($this->currentQuestionIndex > 0) {

            // Auto-save: penting dipanggil setiap ganti halaman soal
            $this->updatedAnswers();

            $this->currentQuestionIndex--;
            session()->put('ujian_index_'.$this->exam->id, $this->currentQuestionIndex);
        }
    }

    public function jumpToQuestion($index)
    {
        // Pastikan nomor yang dituju valid (ada di dalam array soal)
        if ($index >= 0 && $index < count($this->questionsData)) {
            $this->currentQuestionIndex = $index;
            session()->put('ujian_index_'.$this->exam->id, $this->currentQuestionIndex);
        }
    }

    // Fitur Ragu-Ragu
    public function toggleDoubt()
    {
        $qId = $this->questionsData[$this->currentQuestionIndex]['id'];
        if (in_array($qId, $this->doubtfulQuestions)) {
            $this->doubtfulQuestions = array_diff($this->doubtfulQuestions, [$qId]);
        } else {
            $this->doubtfulQuestions[] = $qId;
        }
        session()->put('ujian_doubtful_'.$this->exam->id, $this->doubtfulQuestions);
    }

    // Fitur Hapus Jawaban
    public function clearAnswer()
    {
        $qId = $this->questionsData[$this->currentQuestionIndex]['id'];

        if ($this->questionsData[$this->currentQuestionIndex]['type'] === 'pg_kompleks') {
            $this->answers[$qId] = [];
        } else {
            unset($this->answers[$qId]);
        }

        $this->updatedAnswers();
    }

    public function submitExam()
    {
        $grader = app(ExamGrader::class);

        // Penegakan waktu di sisi SERVER: jika batas waktu sudah lewat,
        // nilai hanya dari jawaban terakhir yang tersimpan sebelum deadline
        // (timer di browser bisa dimanipulasi, session tidak)
        if ($this->isTimeUp()) {
            $this->answers = session()->get('ujian_answers_'.$this->exam->id, $this->answers);
        }

        $questions = $this->exam->questions()->with('options')->get();
        $nilaiBulat = (int) round($grader->autoScore($questions, $this->answers));

        // firstOrCreate + unique index di DB mencegah nilai ganda
        // saat timer habis bersamaan dengan klik tombol submit
        Result::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'exam_id' => $this->exam->id,
            ],
            [
                'score' => $nilaiBulat,
                'answers_data' => $this->answers, // json array menyimpan seluruh histori jawaban essay & pilihan
            ]
        );

        event(new \App\Events\StudentExamUpdate(
            $this->exam->id,
            auth()->user()->name,
            'Telah Mengumpulkan Ujian dengan Nilai: '.$nilaiBulat
        ));

        session()->forget([
            'ujian_selesai_'.$this->exam->id,
            'ujian_questions_'.$this->exam->id,
            'ujian_answers_'.$this->exam->id,
            'ujian_doubtful_'.$this->exam->id,
            'ujian_index_'.$this->exam->id,
            'ujian_strikes_'.$this->exam->id,
            'pin_verified_'.$this->exam->id,
        ]);
        session()->flash('sukses', 'Ujian selesai! Nilai kamu: '.$nilaiBulat);

        return redirect()->route('dashboard');
    }

    public function registerViolation($type = 'tab')
    {
        // Hitungan pelanggaran dinaikkan dan disimpan di SERVER,
        // jadi refresh halaman tidak memberi "nyawa baru"
        $reason = $type === 'fullscreen'
            ? 'Keluar dari Mode Layar Penuh'
            : 'Keluar dari Layar Ujian';

        $key = 'ujian_strikes_'.$this->exam->id;
        $this->cheatStrikes = session()->get($key, 0) + 1;
        session()->put($key, $this->cheatStrikes);

        // Broadcast peringatan ke radar guru
        event(new \App\Events\StudentExamUpdate(
            $this->exam->id,
            auth()->user()->name,
            'PELANGGARAN #'.$this->cheatStrikes.' - '.$reason.'!'
        ));

        if ($this->cheatStrikes >= self::MAX_STRIKES) {
            return $this->submitExam();
        }
    }

    public function render()
    {
        return view('livewire.take-exam');
    }
}
