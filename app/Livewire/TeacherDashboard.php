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
        ]);

        Exam::create([
            'title' => $this->title,
            'description' => $this->description,
            'time_limit' => $this->time_limit,
        ]);

        $this->resetForm();
        session()->flash('sukses', 'Ujian baru berhasil dibuat!');
    }

    public function editExam($id)
    {
        $exam = Exam::findOrFail($id);
        $this->examIdToEdit = $exam->id;
        $this->title = $exam->title;
        $this->description = $exam->description;
        $this->time_limit = $exam->time_limit;

        $this->isEditMode = true;
    }

    public function updateExam()
    {
        $this->validate([
            'title' => 'required|min:5',
            'time_limit' => 'required|numeric|min:1',
        ]);

        $exam = Exam::findOrFail($this->examIdToEdit);
        $exam->update([
            'title' => $this->title,
            'description' => $this->description,
            'time_limit' => $this->time_limit,
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
            Exam::findOrFail($this->examIdToDelete)->delete();
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
        $this->reset(['title', 'description', 'time_limit', 'isEditMode', 'examIdToEdit']);
        $this->time_limit = 60;
    }

    public function toggleStatus($id)
    {
        $exam = Exam::findOrFail($id);
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
        // 1. Logika Pencarian Pintar
        $query = Exam::query();
        if (strlen($this->search) > 0) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }
        $exams = $query->latest()->get();

        // 2. Tarik Data Statistik
        $totalUjian = Exam::count();
        $ujianAktif = Exam::where('is_active', true)->count();
        $totalSoal = Question::count(); // Menghitung total semua soal di database

        return view('livewire.teacher-dashboard', compact('exams', 'totalUjian', 'ujianAktif', 'totalSoal'));
    }
}
