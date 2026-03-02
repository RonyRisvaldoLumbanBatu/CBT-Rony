<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Option;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ItemAnalysis extends Component
{
    public Exam $exam;
    public $analysisData = [];
    public $totalParticipants = 0;

    public function mount($id)
    {
        $this->exam = Exam::with(['questions.options', 'results'])->findOrFail($id);
        $this->calculateAnalysis();
    }

    private function calculateAnalysis()
    {
        $results = $this->exam->results;
        $this->totalParticipants = $results->count();

        // Jika belum ada yang ikut ujian, lewati perhitungan
        if ($this->totalParticipants === 0) {
            return;
        }

        foreach ($this->exam->questions as $question) {
            $benar = 0;
            $salah = 0;
            $distribusiOpsi = []; // Untuk melacak jumlah siswa yang memilih Tiap Opsi (A,B,C,D)

            // Inisialisasi daftar opsi untuk grafik batang
            if (in_array($question->type, ['pg', 'benar_salah'])) {
                foreach ($question->options as $opt) {
                    $distribusiOpsi[$opt->id] = [
                        'text' => $opt->option_text,
                        'is_correct' => $opt->is_correct,
                        'count' => 0
                    ];
                }
            }

            // Loop untuk mengecek jawaban setiap peserta
            foreach ($results as $result) {
                // answers_data format: ['question_id' => 'user_answer']
                $answersData = $result->answers_data ?? [];

                if (!isset($answersData[$question->id])) {
                    $salah++; // Tidak dijawab dianggap salah
                    continue;
                }

                $userAns = $answersData[$question->id];

                // Hitung Tingkat Kebenaran berdasarkan Tipe Soal
                if ($question->type === 'pg' || $question->type === 'benar_salah') {
                    // Update Distribusi Opsi jika PG / B-S
                    if (isset($distribusiOpsi[$userAns])) {
                        $distribusiOpsi[$userAns]['count']++;
                    }

                    $isCorrect = Option::where('id', $userAns)->where('is_correct', true)->exists();
                    if ($isCorrect) {
                        $benar++;
                    } else {
                        $salah++;
                    }
                } elseif ($question->type === 'pg_kompleks' && is_array($userAns)) {
                    $correctOptionIds = Option::where('question_id', $question->id)->where('is_correct', true)->pluck('id')->toArray();
                    sort($userAns);
                    sort($correctOptionIds);
                    if ($userAns == $correctOptionIds) {
                        $benar++;
                    } else {
                        $salah++;
                    }
                } elseif ($question->type === 'isian') {
                    $correctOption = Option::where('question_id', $question->id)->where('is_correct', true)->first();
                    if ($correctOption && strtolower(trim($userAns)) === strtolower(trim($correctOption->option_text))) {
                        $benar++;
                    } else {
                        $salah++;
                    }
                } elseif ($question->type === 'essay') {
                    // Cek tabel essay_scores dari result
                    $essayScores = $result->essay_scores ?? [];
                    if (isset($essayScores[$question->id]) && $essayScores[$question->id] >= 60) {
                        // Kriteria Essay 'Benar' dinilai KKM >= 60
                        $benar++;
                    } else {
                        $salah++;
                    }
                }
            }

            // Tingkat Kesukaran (Difficulty Index)
            // Rumus D = Jumlah Siswa Jawab Benar / Total Peserta Tertinggi -> Dikalikan 100%
            $tingkatKesukaran = ($benar / $this->totalParticipants) * 100;

            // Evaluasi Kesulitan (Validasi Butir Soal kriteria Eval klasik)
            $kategoriKesulitan = 'Sedang'; // 30% - 70%
            $warnaLabel = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';

            if ($tingkatKesukaran > 70) {
                $kategoriKesulitan = 'Mudah'; // > 70% Mudah
                $warnaLabel = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            } elseif ($tingkatKesukaran < 30) {
                $kategoriKesulitan = 'Sukar'; // < 30% Sulit
                $warnaLabel = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
            }

            $this->analysisData[] = [
                'question' => $question,
                'type_label' => $this->getTypeLabel($question->type),
                'benar' => $benar,
                'salah' => $salah,
                'tingkat_kesukaran' => round($tingkatKesukaran, 2),
                'kategori_kesulitan' => $kategoriKesulitan,
                'warna_label' => $warnaLabel,
                'distribusi' => $distribusiOpsi,
            ];
        }
    }

    private function getTypeLabel($type)
    {
        return match ($type) {
            'pg' => 'Pilihan Ganda',
            'pg_kompleks' => 'PG Kompleks',
            'benar_salah' => 'Benar / Salah',
            'isian' => 'Isian Singkat',
            'essay' => 'Uraian (Essay)',
            default => 'Unknown'
        };
    }

    public function render()
    {
        return view('livewire.item-analysis');
    }
}
