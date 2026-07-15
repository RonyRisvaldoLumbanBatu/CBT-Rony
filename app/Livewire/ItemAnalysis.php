<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Services\ExamGrader;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ItemAnalysis extends Component
{
    public Exam $exam;

    public $analysisData = [];

    public $totalParticipants = 0;

    public function mount($id, ExamGrader $grader)
    {
        $this->exam = Exam::with(['questions.options', 'results'])->findOrFail($id);
        $this->calculateAnalysis($grader);
    }

    private function calculateAnalysis(ExamGrader $grader)
    {
        $results = $this->exam->results;
        $this->totalParticipants = $results->count();

        // Jika belum ada yang ikut ujian, lewati perhitungan
        if ($this->totalParticipants === 0) {
            return;
        }

        foreach ($this->exam->questions as $question) {
            $benar = 0;

            // Loop untuk mengecek jawaban setiap peserta
            // (options sudah di-eager-load, jadi tidak ada query di dalam loop)
            foreach ($results as $result) {
                // answers_data format: ['question_id' => 'user_answer']
                $answersData = $result->answers_data ?? [];

                if ($question->type === 'essay') {
                    // Kriteria Essay 'Benar' dinilai KKM >= 60 dari tabel essay_scores
                    $essayScores = $result->essay_scores ?? [];
                    if (isset($essayScores[$question->id]) && $essayScores[$question->id] >= 60) {
                        $benar++;
                    }
                } elseif (isset($answersData[$question->id])
                    && $grader->isAnswerCorrect($question, $answersData[$question->id])) {
                    $benar++;
                }
            }

            // Tidak dijawab / jawaban salah sama-sama dihitung salah
            $salah = $this->totalParticipants - $benar;

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
