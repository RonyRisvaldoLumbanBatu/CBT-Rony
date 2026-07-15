<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Result;
use App\Services\ExamGrader;
use Livewire\Attributes\Layout;
use Livewire\Component;

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
        $this->exam = Exam::with('questions.options')->findOrFail($id);
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

    public function saveScores(ExamGrader $grader)
    {
        // Validasi input max 100
        $this->validate([
            'essayScores.*' => 'required|numeric|min:0|max:100',
        ]);

        // Kalkulasi ulang seluruh nilai (logika sama persis dengan TakeExam
        // karena keduanya memakai ExamGrader yang sama)
        $answersData = $this->selectedResult->answers_data ?? [];
        $nilaiBulat = $grader->finalScore($this->exam->questions, $answersData, $this->essayScores);

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
            'hasEssay' => $hasEssay,
        ]);
    }
}
