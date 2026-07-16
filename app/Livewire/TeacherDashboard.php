<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Question;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TeacherDashboard extends Component
{
    // Variabel penampung form
    public $title = '';

    public $description = '';

    public $time_limit = 60;

    // Target kelas ujian; string kosong = semua kelas
    public $classroom_id = '';

    // Variabel khusus mode Edit & Hapus
    public $isEditMode = false;

    public $examIdToEdit = null;

    public $isDeleteModalOpen = false;

    public $examIdToDelete = null;

    // === VARIABEL BARU UNTUK FITUR PENCARIAN ===
    public $search = '';

    public function createExam()
    {
        $this->validate([
            'title' => 'required|min:5',
            'time_limit' => 'required|numeric|min:1',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        Exam::create([
            'teacher_id' => auth()->id(),
            'classroom_id' => $this->classroom_id ?: null,
            'title' => $this->title,
            'description' => $this->description,
            'time_limit' => $this->time_limit,
        ]);

        $this->resetForm();
        session()->flash('sukses', 'Ujian baru berhasil dibuat!');
    }

    // Ambil ujian sekaligus pastikan miliknya guru yang sedang login
    private function findOwnedExam($id): Exam
    {
        $exam = Exam::findOrFail($id);
        abort_unless($exam->isOwnedBy(auth()->user()), 403, 'Ujian ini milik guru lain.');

        return $exam;
    }

    public function editExam($id)
    {
        $exam = $this->findOwnedExam($id);
        $this->examIdToEdit = $exam->id;
        $this->title = $exam->title;
        $this->description = $exam->description;
        $this->time_limit = $exam->time_limit;
        $this->classroom_id = $exam->classroom_id ?? '';

        $this->isEditMode = true;
    }

    public function updateExam()
    {
        $this->validate([
            'title' => 'required|min:5',
            'time_limit' => 'required|numeric|min:1',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        $exam = $this->findOwnedExam($this->examIdToEdit);
        $exam->update([
            'title' => $this->title,
            'description' => $this->description,
            'time_limit' => $this->time_limit,
            'classroom_id' => $this->classroom_id ?: null,
        ]);

        $this->resetForm();
        session()->flash('sukses', 'Data ujian berhasil diperbarui!');
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->examIdToDelete = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete()
    {
        if ($this->examIdToDelete) {
            $this->findOwnedExam($this->examIdToDelete)->delete();
            session()->flash('sukses', 'Ujian berhasil dihapus permanen!');
        }
        $this->isDeleteModalOpen = false;
        $this->examIdToDelete = null;
    }

    public function cancelDelete()
    {
        $this->isDeleteModalOpen = false;
        $this->examIdToDelete = null;
    }

    private function resetForm()
    {
        $this->reset(['title', 'description', 'time_limit', 'isEditMode', 'examIdToEdit', 'classroom_id']);
        $this->time_limit = 60;
    }

    public function toggleStatus($id)
    {
        $exam = $this->findOwnedExam($id);
        $newStatus = ! $exam->is_active;

        // Buat PIN dinamis (6 Karakter alphanumeric) saat Ujian diaktifkan
        $tokenPin = $newStatus ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)) : null;

        $exam->update([
            'is_active' => $newStatus,
            'token' => $tokenPin,
        ]);

        $status = $newStatus ? 'dibuka' : 'ditutup';
        session()->flash('sukses', "Ujian '{$exam->title}' berhasil $status! PIN Ujian baru: ".($tokenPin ?? '-'));
    }

    public function render()
    {
        // 1. Logika Pencarian Pintar (hanya ujian milik guru ini)
        $query = Exam::with('classroom')->ownedBy(auth()->user());
        if (strlen($this->search) > 0) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }
        $exams = $query->latest()->get();

        // 2. Tarik Data Statistik (scoped ke ujian milik guru ini)
        $totalUjian = Exam::ownedBy(auth()->user())->count();
        $ujianAktif = Exam::ownedBy(auth()->user())->where('is_active', true)->count();
        $totalSoal = Question::whereHas('exam', fn ($q) => $q->ownedBy(auth()->user()))->count();

        $classrooms = \App\Models\Classroom::orderBy('name')->get();

        return view('livewire.teacher-dashboard', compact('exams', 'totalUjian', 'ujianAktif', 'totalSoal', 'classrooms'));
    }
}
