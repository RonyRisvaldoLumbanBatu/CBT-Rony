<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    // Mengatur nama-nama kolom (Header)
    public function headings(): array
    {
        return [
            'jenis_soal', // Wajib: 'pg', 'pg_kompleks', 'benar_salah', 'isian', atau 'essay'
            'soal',       // Teks soal
            'opsi_a',     // Kosongkan jika bukan pilihan ganda
            'opsi_b',     // Kosongkan jika bukan pilihan ganda
            'opsi_c',     // Kosongkan jika bukan pilihan ganda
            'opsi_d',     // Kosongkan jika bukan pilihan ganda
            'kunci_jawaban'// Kunci Jawaban. Cara isinya berbeda tergantung jenis_soal (lihat sampel)
        ];
    }

    // Mengisi baris pertama sebagai contoh pengisian untuk Guru
    public function array(): array
    {
        return [
            [
                'pg',
                'Apa ibu kota negara Republik Indonesia?',
                'Bandung',
                'Surabaya',
                'Jakarta',
                'Medan',
                'C'
            ],
            [
                'pg_kompleks',
                'Manakah dari berikut ini yang merupakan bahasa pemrograman?',
                'HTML',
                'Python',
                'CSS',
                'Java',
                'B,D' // Dipisah dengan koma, tanpa spasi setelahnya boleh
            ],
            [
                'benar_salah',
                'Matahari terbit dari barat.',
                '',
                '',
                '',
                '',
                'Salah' // Isi dengan 'Benar' atau 'Salah'
            ],
            [
                'isian',
                'Siapa nama presiden pertama Republik Indonesia?',
                '',
                '',
                '',
                '',
                'Soekarno' // Ketik string kunci jawaban presisi
            ],
            [
                'essay',
                'Jelaskan secara singkat apa yang dimaksud dengan Fotosintesis!',
                '',
                '',
                '',
                '',
                '' // Kosongkan
            ],
        ];
    }
}