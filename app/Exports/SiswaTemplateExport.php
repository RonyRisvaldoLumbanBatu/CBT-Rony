<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function headings(): array
    {
        return [
            'nama',       // Wajib: nama lengkap siswa
            'username',   // Wajib: unik, untuk login (huruf/angka/titik/strip)
            'nis',        // Opsional: nomor induk siswa
            'email',      // Opsional: kosongkan jika siswa tidak punya email
            'password',   // Wajib: minimal 8 karakter (dicetak di kartu peserta)
            'kelas',      // Wajib jika ada: HARUS sama persis dengan nama kelas di Panel Admin
        ];
    }

    public function array(): array
    {
        return [
            ['Budi Santoso', 'budi.santoso', '2026001', 'budi@contoh.com', 'password123', 'XII IPA 1'],
            ['Ani Wijaya', 'ani.wijaya', '2026002', '', 'password123', 'XII IPA 2'],
        ];
    }
}
