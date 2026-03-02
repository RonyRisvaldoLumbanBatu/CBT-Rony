<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Question;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GradeExam extends Component
{
    public Exam $exam;

    // Untuk Modal / Data spesifik
    public $isModalOpen = false;
    public $selectedResult;
    public $essayQuestions = [];
    public $essayAnswers = [];
    public $essayScores = []; // ex: [question_id => score]

    public function mount($id)
    {
        $this->exam = Exam::with('questions')->findOrFail($id);
    }

    public function openGradeModal($resultId)
    {
        $this->selectedResult = Result::with('user')->findOrFail($resultId);

        // Filter hanya pertanyaan essay
        $this->essayQuestions = $this->exam->questions->where('type', 'essay')->values();

        $answersData = $this->selectedResult->answers_data ?? [];
        $existingScores = $this->selectedResult->essay_scores ?? [];

        $this->essayAnswers = [];
        $this->essayScores = [];

        foreach ($this->essayQuestions as $q) {
            $this->essayAnswers[$q->id] = $answersData[$q->id] ?? '- Siswa tidak menjawab -';
            $this->essayScores[$q->id] = $existingScores[$q->id] ?? 0;
        }

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedResult = null;
    }

    public function saveScores()
    {
        // Validasi input max 100
        $this->validate([
            'essayScores.*' => 'required|numeric|min:0|max:100',
        ]);

        // Kalkulasi ulang seluruh nilai agar valid dan adil!
        $allQuestions = $this->exam->questions;
        $tipeOtomatis = ['pg', 'pg_kompleks', 'benar_salah', 'isian'];

        $soalOtomatis = $allQuestions->whereIn('type', $tipeOtomatis);
        $totalSoalOtomatis = $soalOtomatis->count();
        $totalEssay = $this->essayQuestions->count();

        $answersData = $this->selectedResult->answers_data ?? [];
        $jawabanBenar = 0;

        // Hitung ulang PG persis seperti di TakeExam.php (Mencegah bug sinkronisasi nilai)
        foreach ($soalOtomatis as $q) {
            if (!isset($answersData[$q->id]))
                continue;

            $userAns = $answersData[$q->id];

            if ($q->type === 'pg' || $q->type === 'benar_salah') {
                $isCorrect = \App\Models\Option::where('id', $userAns)->where('is_correct', true)->exists();
                if ($isCorrect)
                    $jawabanBenar++;
            } elseif ($q->type === 'pg_kompleks' && is_array($userAns)) {
                $correctOptionIds = \App\Models\Option::where('question_id', $q->id)->where('is_correct', true)->pluck('id')->toArray();
                sort($userAns);
                sort($correctOptionIds);
                if ($userAns == $correctOptionIds)
                    $jawabanBenar++;
            } elseif ($q->type === 'isian') {
                $correctOption = \App\Models\Option::where('question_id', $q->id)->where('is_correct', true)->first();
                if ($correctOption && strtolower(trim($userAns)) === strtolower(trim($correctOption->option_text))) {
                    $jawabanBenar++;
                }
            }
        }

        // Skor otomatis out of 100 (PG Max 100, Jika tidak ada PG = 0)
        $skorPG = $totalSoalOtomatis > 0 ? ($jawabanBenar / $totalSoalOtomatis) * 100 : 0;

        // Skor Essay
        $sumEssay = array_sum($this->essayScores); // Total nilai semua essay
        $skorEssayRataRata = $totalEssay > 0 ? ($sumEssay / $totalEssay) : 0;

        // Gabungan
        // Skenario A: Ada PG ada Essay -> (SkorPG + SkorEssay) / 2
        // Skenario B: Hanya PG -> SkorPG
        // Skenario C: Hanya Essay -> SkorEssayRataRata
        $nilaiAkhir = 0;
        if ($totalSoalOtomatis > 0 && $totalEssay > 0) {
            $nilaiAkhir = ($skorPG + $skorEssayRataRata) / 2;
        } elseif ($totalSoalOtomatis > 0) {
            $nilaiAkhir = $skorPG;
        } elseif ($totalEssay > 0) {
            $nilaiAkhir = $skorEssayRataRata;
        }

        $nilaiBulat = (int) round($nilaiAkhir);

        // Update database!
        $this->selectedResult->update([
            'score' => $nilaiBulat,
            'essay_scores' => $this->essayScores,
            'is_essay_graded' => true,
        ]);

        $this->closeModal();
        session()->flash('sukses', 'Penilaian Essay Kelas Berhasil Disimpan!');
    }

    public function render()
    {
        // Panggil hanya results ujian ini
        $results = Result::with('user')->where('exam_id', $this->exam->id)->latest()->get();
        // Cek total soal essay
        $hasEssay = $this->exam->questions->where('type', 'essay')->count() > 0;

        return view('livewire.grade-exam', [
            'results' => $results,
            'hasEssay' => $hasEssay
        ]);
    }
}
