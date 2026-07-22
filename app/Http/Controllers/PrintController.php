<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Exam;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PrintController extends Controller
{
    /**
     * Kartu peserta ujian per kelas (username + password login).
     * Khusus admin.
     */
    public function participantCards(int $classroomId): Response
    {
        $classroom = Classroom::with(['major', 'students' => fn ($q) => $q->orderBy('name')])
            ->findOrFail($classroomId);

        $pdf = Pdf::loadView('pdf.kartu-peserta', compact('classroom'));

        return $pdf->download('Kartu_Peserta_'.str_replace(' ', '_', $classroom->name).'.pdf');
    }

    /**
     * Daftar hadir peserta suatu ujian (untuk ditandatangani).
     * Khusus guru pemilik ujian.
     */
    public function attendanceList(int $examId): Response
    {
        $exam = Exam::with(['classroom', 'subject', 'teacher'])->findOrFail($examId);
        abort_unless($exam->isOwnedBy(auth()->user()), 403, 'Ujian ini milik guru lain.');

        // Peserta = siswa kelas target (atau semua siswa jika ujian umum)
        $participants = \App\Models\User::where('role', 'siswa')
            ->when($exam->classroom_id, fn ($q) => $q->where('classroom_id', $exam->classroom_id))
            ->with('classroom')
            ->orderBy('name')
            ->get();

        $results = $exam->results()->get()->keyBy('user_id');

        $pdf = Pdf::loadView('pdf.daftar-hadir', compact('exam', 'participants', 'results'));

        return $pdf->download('Daftar_Hadir_'.str_replace(' ', '_', $exam->title).'.pdf');
    }

    /**
     * Berita acara pelaksanaan ujian.
     * Khusus guru pemilik ujian.
     */
    public function officialReport(int $examId): Response
    {
        $exam = Exam::with(['classroom', 'subject', 'teacher'])
            ->withCount('results')
            ->findOrFail($examId);
        abort_unless($exam->isOwnedBy(auth()->user()), 403, 'Ujian ini milik guru lain.');

        $totalPeserta = \App\Models\User::where('role', 'siswa')
            ->when($exam->classroom_id, fn ($q) => $q->where('classroom_id', $exam->classroom_id))
            ->count();

        $pdf = Pdf::loadView('pdf.berita-acara', compact('exam', 'totalPeserta'));

        return $pdf->download('Berita_Acara_'.str_replace(' ', '_', $exam->title).'.pdf');
    }
}
