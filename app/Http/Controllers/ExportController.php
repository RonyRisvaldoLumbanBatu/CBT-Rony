<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use Barryvdh\DomPDF\Facade\Pdf; // Panggil mesin PDF-nya

class ExportController extends Controller
{
    public function exportPdf($examId)
    {
        // 1. Tarik data ujiannya
        $exam = Exam::findOrFail($examId);

        // 2. Tarik semua nilai siswa untuk ujian ini, urutkan dari nilai tertinggi
        $results = Result::with('user')
            ->where('exam_id', $examId)
            ->orderByDesc('score')
            ->get();

        // 3. Masukkan datanya ke dalam desain kertas PDF
        $pdf = Pdf::loadView('pdf.exam-result', compact('exam', 'results'));

        // 4. Perintahkan browser untuk mendownload filenya
        return $pdf->download('Rekap_Nilai_'.str_replace(' ', '_', $exam->title).'.pdf');
    }

    public function exportExcel($examId)
    {
        $exam = Exam::findOrFail($examId);
        $fileName = 'Rekap_Nilai_'.str_replace(' ', '_', $exam->title).'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExamResultsExport($examId), $fileName);
    }
}
