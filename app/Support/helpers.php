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

if (! function_exists('app_setting')) {
    /**
     * Baca pengaturan aplikasi dari tabel settings (aman dipanggil
     * sebelum migrasi jalan — fallback ke default).
     */
    function app_setting(string $key, ?string $default = null): ?string
    {
        try {
            return Setting::get($key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (! function_exists('cbt_name')) {
    /** Nama aplikasi ujian (bisa diubah admin di Panel Admin). */
    function cbt_name(): string
    {
        return app_setting('app_name', 'UjianPintar');
    }
}

if (! function_exists('cbt_institution')) {
    /** Nama sekolah/kampus (bisa diubah admin di Panel Admin). */
    function cbt_institution(): string
    {
        return app_setting('institution_name', term('sekolah').' Pintar Nusantara');
    }
}

if (! function_exists('cbt_logo_url')) {
    /** URL logo/icon aplikasi, null jika admin belum upload. */
    function cbt_logo_url(): ?string
    {
        $path = app_setting('logo_path');

        return $path ? \Illuminate\Support\Facades\Storage::url($path) : null;
    }
}

if (! function_exists('cbt_banner_url')) {
    /** URL banner halaman depan, null jika admin belum upload. */
    function cbt_banner_url(): ?string
    {
        $path = app_setting('banner_path');

        return $path ? \Illuminate\Support\Facades\Storage::url($path) : null;
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
