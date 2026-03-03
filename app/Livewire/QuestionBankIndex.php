<?php

namespace App\Livewire;

use App\Models\QuestionBank;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class QuestionBankIndex extends Component
{
    public $title = '';

    public $description = '';

    public $isModalOpen = false;

    // Untuk validasi pembuatan Bank Soal baru
    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];

    public function createBank()
    {
        $this->validate();

        QuestionBank::create([
            'title' => $this->title,
            'description' => $this->description,
            'teacher_id' => auth()->id(),
        ]);

        $this->reset(['title', 'description']);
        $this->isModalOpen = false;

        session()->flash('message', 'Bank Soal berhasil dibuat!');
    }

    public function deleteBank($id)
    {
        $bank = QuestionBank::where('id', $id)->where('teacher_id', auth()->id())->firstOrFail();
        $bank->delete();

        session()->flash('message', 'Bank Soal berhasil dihapus!');
    }

    public function render()
    {
        // Hanya ambil Bank Soal milik guru yang sedang login
        $banks = QuestionBank::withCount('questions')
            ->where('teacher_id', auth()->id())
            ->latest()
            ->get();

        return view('livewire.question-bank-index', compact('banks'));
    }
}
