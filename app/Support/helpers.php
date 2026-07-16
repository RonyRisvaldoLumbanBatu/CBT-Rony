<?php

use App\Models\Setting;

if (! function_exists('app_mode')) {
    /**
     * Mode aplikasi: 'sekolah' atau 'kampus'.
     * Menentukan istilah yang dipakai di seluruh UI (Siswa/Mahasiswa, Guru/Dosen, dst).
     */
    function app_mode(): string
    {
        try {
            return Setting::get('institution_mode', 'sekolah');
        } catch (\Throwable) {
            // Tabel settings belum ada (mis. saat migrasi pertama) -> fallback aman
            return 'sekolah';
        }
    }
}

if (! function_exists('term')) {
    /**
     * Terjemahkan istilah sesuai mode aplikasi.
     * term('siswa') -> 'Siswa' (mode sekolah) / 'Mahasiswa' (mode kampus)
     */
    function term(string $key): string
    {
        $terms = [
            'sekolah' => [
                'siswa' => 'Siswa',
                'guru' => 'Guru',
                'sekolah' => 'Sekolah',
                'kelas' => 'Kelas',
            ],
            'kampus' => [
                'siswa' => 'Mahasiswa',
                'guru' => 'Dosen',
                'sekolah' => 'Kampus',
                'kelas' => 'Kelas',
            ],
        ];

        return $terms[app_mode()][strtolower($key)] ?? ucfirst($key);
    }
}
