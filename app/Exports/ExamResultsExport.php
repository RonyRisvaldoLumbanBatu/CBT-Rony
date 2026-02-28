<?php

namespace App\Exports;

use App\Models\Result;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamResultsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $examId;
    protected $rank = 1;

    public function __construct($examId)
    {
        $this->examId = $examId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Result::with('user')
            ->where('exam_id', $this->examId)
            ->orderByDesc('score')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Peringkat',
            'Nama Mahasiswa',
            'NIM/Email',
            'Nilai Akhir',
            'Waktu Pengumpulan'
        ];
    }

    public function map($result): array
    {
        return [
            $this->rank++,
            $result->user->name,
            $result->user->email,
            $result->score,
            $result->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i:s')
        ];
    }
}
