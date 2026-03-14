<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * Export the exam results to a PDF file.
     */
    public function exportPdf(int $examId): Response
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

    /**
     * Export the exam results to an Excel file.
     */
    public function exportExcel(int $examId): BinaryFileResponse
    {
        $exam = Exam::findOrFail($examId);
        $fileName = 'Rekap_Nilai_'.str_replace(' ', '_', $exam->title).'.xlsx';

        return Excel::download(new \App\Exports\ExamResultsExport($examId), $fileName);
    }
}
