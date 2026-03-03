<?php

namespace App\Livewire;

use App\Models\Exam;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class ProctorDashboard extends Component
{
    public Exam $exam;

    public $logs = []; // Array penampung laporan aktivitas real-time

    public function mount($id)
    {
        $this->exam = Exam::findOrFail($id);
    }

    // MAGIC LIVEWIRE: Fungsi ini otomatis mendengarkan saluran radio Reverb!
    #[On('echo:exam-monitor.{exam.id},StudentExamUpdate')]
    public function tangkapSinyalSiswa($dataSinyal)
    {
        // Masukkan laporan baru ke posisi paling atas (unshift) array logs
        array_unshift($this->logs, [
            'nama' => $dataSinyal['studentName'],
            'pesan' => $dataSinyal['statusMessage'],
            'waktu' => now()->format('H:i:s'),
        ]);
    }

    public function render()
    {
        return view('livewire.proctor-dashboard');
    }
}
