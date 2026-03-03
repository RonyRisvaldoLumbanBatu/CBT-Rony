<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Result;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TakeExam extends Component
{
    public Exam $exam;

    public $currentQuestionIndex = 0;

    public $answers = [];

    public $timeLeft;

    // Variabel antarmuka untuk PIN
    public $isPinVerified = false;

    public $inputPin = '';

    // Variabel baru untuk menampung soal yang sudah diacak
    public $questionsData = [];

    public function verifyPin()
    {
        if (strtoupper($this->inputPin) === strtoupper($this->exam->token)) {
            $this->isPinVerified = true;
            session()->put('pin_verified_'.$this->exam->id, true);
        } else {
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

        // Verifikasi PIN
        if (empty($this->exam->token)) {
            $this->isPinVerified = true; // Jika guru belum set PIN, langsung loncat
        } else {
            $this->isPinVerified = session()->get('pin_verified_'.$id, false);
        }

        // 3. MESIN PENGURUTAN TIPE & PENGACAK SOAL (ANTI-NYONTEK)
        $acakSoal = [];

        // Urutan tipe soal yang diinginkan: PG -> PG Kompleks -> Benar/Salah -> Isian -> Essay
        $typeOrder = ['pg' => 1, 'pg_kompleks' => 2, 'benar_salah' => 3, 'isian' => 4, 'essay' => 5];

        // Acak semua soal dulu, lalu di-sort berdasarkan tipe
        // Ini memastikan soal setiap murid tetap acak isinya, TAPI urutan tipenya terstruktur
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

    public function nextQuestion()
    {
        // Sesuaikan validasi jumlah soal dengan array baru
        if ($this->currentQuestionIndex < count($this->questionsData) - 1) {
            $this->currentQuestionIndex++;

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
            $this->currentQuestionIndex--;
        }
    }

    public function jumpToQuestion($index)
    {
        // Pastikan nomor yang dituju valid (ada di dalam array soal)
        if ($index >= 0 && $index < count($this->questionsData)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function submitExam()
    {
        $jawabanBenar = 0;

        // Filter Total Soal yang otomatis dinilai sistem
        $tipeOtomatis = ['pg', 'pg_kompleks', 'benar_salah', 'isian'];
        $totalSoalOtomatis = count(array_filter($this->questionsData, fn ($q) => in_array($q['type'], $tipeOtomatis)));

        foreach ($this->questionsData as $q) {
            if (! isset($this->answers[$q['id']])) {
                continue;
            }

            $userAns = $this->answers[$q['id']];

            if ($q['type'] === 'pg' || $q['type'] === 'benar_salah') {
                $isCorrect = \App\Models\Option::where('id', $userAns)->where('is_correct', true)->exists();
                if ($isCorrect) {
                    $jawabanBenar++;
                }
            } elseif ($q['type'] === 'pg_kompleks') {
                // userAns harus berbentuk array untuk PG Kompleks
                if (is_array($userAns)) {
                    $correctOptionIds = \App\Models\Option::where('question_id', $q['id'])->where('is_correct', true)->pluck('id')->toArray();
                    sort($userAns);
                    sort($correctOptionIds);
                    if ($userAns == $correctOptionIds) {
                        $jawabanBenar++;
                    }
                }
            } elseif ($q['type'] === 'isian') {
                $correctOption = \App\Models\Option::where('question_id', $q['id'])->where('is_correct', true)->first();
                if ($correctOption && is_string($userAns) && strtolower(trim($userAns)) === strtolower(trim($correctOption->option_text))) {
                    $jawabanBenar++;
                }
            }
        }

        // Antisipasi pembagian dengan nol
        $nilaiAkhir = $totalSoalOtomatis > 0 ? ($jawabanBenar / $totalSoalOtomatis) * 100 : 0;
        $nilaiBulat = (int) round($nilaiAkhir);

        Result::create([
            'user_id' => auth()->id(),
            'exam_id' => $this->exam->id,
            'score' => $nilaiBulat,
            'answers_data' => $this->answers, // json array menyimpan seluruh histori jawaban essay & pilihan
        ]);

        event(new \App\Events\StudentExamUpdate(
            $this->exam->id,
            auth()->user()->name,
            'Telah Mengumpulkan Ujian dengan Nilai: '.$nilaiAkhir
        ));

        session()->forget('ujian_selesai_'.$this->exam->id);
        session()->flash('sukses', 'Ujian selesai! Nilai kamu: '.$nilaiAkhir);

        return redirect()->route('dashboard');
    }

    public function logCheatStrike($strikeCount)
    {
        // Broadcast peringatan ke radar guru
        event(new \App\Events\StudentExamUpdate(
            $this->exam->id,
            auth()->user()->name,
            '⚠️ PELANGGARAN #'.$strikeCount.' - Keluar dari Layar Ujian!'
        ));
    }

    public function render()
    {
        return view('livewire.take-exam');
    }
}
