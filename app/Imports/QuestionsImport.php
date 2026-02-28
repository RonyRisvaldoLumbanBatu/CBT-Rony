<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// Memakai WithHeadingRow agar baris pertama Excel dibaca sebagai nama kolom
class QuestionsImport implements ToCollection, WithHeadingRow
{
    public $examId;

    // Menangkap ID Ujian saat file diupload
    public function __construct($examId)
    {
        $this->examId = $examId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Abaikan baris kosong
            if (!isset($row['soal']) || empty($row['soal'])) {
                continue;
            }

            $jenisSoal = isset($row['jenis_soal']) ? strtolower(trim($row['jenis_soal'])) : 'pg';
            $kunci = isset($row['kunci_jawaban']) ? trim($row['kunci_jawaban']) : '';

            // 1. Masukkan data ke tabel Soal
            $question = Question::create([
                'exam_id' => $this->examId,
                'question_text' => $row['soal'],
                'type' => $jenisSoal,
            ]);

            // 2 & 3. Masukkan data ke tabel Options (Berbeda Tergantung Tipe)
            if ($jenisSoal === 'pg' || $jenisSoal === 'pg_kompleks') {
                $pilihan = [
                    'A' => $row['opsi_a'] ?? '',
                    'B' => $row['opsi_b'] ?? '',
                    'C' => $row['opsi_c'] ?? '',
                    'D' => $row['opsi_d'] ?? '',
                ];

                $arrayKunci = [];
                if ($jenisSoal === 'pg_kompleks') {
                    $arrayKunci = explode(',', strtoupper($kunci));
                    $arrayKunci = array_map('trim', $arrayKunci);
                }

                foreach ($pilihan as $huruf => $teksOpsi) {
                    $isCorrect = false;
                    if ($jenisSoal === 'pg') {
                        $isCorrect = strtoupper($kunci) === $huruf;
                    } else {
                        $isCorrect = in_array($huruf, $arrayKunci);
                    }

                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $teksOpsi,
                        'is_correct' => $isCorrect,
                    ]);
                }
            } elseif ($jenisSoal === 'benar_salah') {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => 'Benar',
                    'is_correct' => strtolower($kunci) === 'benar',
                ]);
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => 'Salah',
                    'is_correct' => strtolower($kunci) === 'salah',
                ]);
            } elseif ($jenisSoal === 'isian') {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $kunci,
                    'is_correct' => true,
                ]);
            }
        }
    }
}