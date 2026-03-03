<div align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    <h1 align="center">Computer Based Test (CBT) System</h1>
    <p align="center">
        Aplikasi Ujian Berbasis Komputer Modern menggunakan <b>Laravel 12</b>, <b>Livewire 3</b>, dan <b>Tailwind CSS v3</b>.
        <br />
        <a href="#fitur-utama"><strong>Jelajahi Fitur »</strong></a>
        <br />
        <br />
    </p>
</div>

---

## 🚀 Tentang Aplikasi

**CBT Rony** adalah platform Computer Based Test modern yang dirancang untuk memudahkan manajemen ujian sekolah maupun kampus. Aplikasi ini mendukung **Tiga Peran Utama** (Admin/Guru, Pengawas, dan Siswa) serta menyediakan antarmuka pengisian ujian yang cepat, responsif, tanpa *loading* ulang halaman (Single Page Application-like) berkat **Laravel Livewire 3**.

Dibekali dengan **5 variasi tipe soal ujian standar AKM & UN**, pengacakan otomatis, batas waktu, dan fitur pencegahan kecurangan berbasis PIN/Token!

## ✨ Fitur Utama

- 🔐 **Multi-Role Authentication** (Guru / Dosen, Pengawas, dan Siswa).
- 📝 **5 Tipe Soal Terintegrasi:**
  1. **Pilihan Ganda Biasa** (1 Jawaban Benar)
  2. **Pilihan Ganda Kompleks** (Banyak Centang / Jawaban Benar)
  3. **Benar / Salah** (True / False)
  4. **Isian Singkat** (Dinilai Presisi secara Otomatis)
  5. **Uraian / Essay** (Koreksi Subjektif Manual & Fleksibel)
- 📊 **Analisis Butir Soal:** Sistem pemetaan *Difficulty Index* otomatis (Mudah, Sedang, Sulit) untuk mengukur rasio kesulitan dari jawaban siswa secara faktual.
- 🏦 **Bank Soal Kategori Dinamis:** Penyimpanan permanen arsip soal yang dapat didaur ulang dan ditarik kembali saat membuat ujian baru tanpa perlu mengetik ulang!
- ⏱️ **Manajemen Ujian Ketat:** Set batas waktu (*Time Limit*), pengaturan visibilitas aktif/pasif, dan Token PIN keamanan anti-bocor.
- 🎲 **Anti-Cheat Logic:** Mode *Question & Options Shuffling* untuk mengacak susunan ujian secara individual antar siswa.
- 📥 **Mass Import & Export (Excel):** Unggah ratusan soal sekaligus via Template Excel, dan unduh rekapan nilai akhir siswa dengan cepat dalam format `.xlsx`.
- 🖨️ **Cetak Hasil Ujian (PDF):** Rekapan data ujian dan nilai siap didownload menjadi arsip Dokumen PDF siap cetak.
- 💅 **UI/UX Modern:** Desain interaktif yang responsif dengan efek kaca (glassmorphism), navigasi `wire:navigate` gaya SPA cepat, menggunakan **Tailwind CSS**.

## 🛠️ Teknologi yang Digunakan

*   [Laravel 12.x](https://laravel.com) - PHP Framework Termutakhir
*   [Livewire 3](https://livewire.laravel.com/) - Dynamic Front-End Framework
*   [Alpine.js](https://alpinejs.dev/) - Lightweight JavaScript Framework
*   [Tailwind CSS v3](https://tailwindcss.com/) - Utility-first CSS Framework
*   [Laravel Excel (Maatwebsite)](https://laravel-excel.com/) - Penangan Import/Export Excel
*   [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) - Generator PDF
*   MySQL - Relational Database

## 💻 Cara Instalasi (Local Development)

Jika Anda ingin menjalankan aplikasi ini di komputer lokal (localhost), ikuti langkah-langkah berikut:

**1. Clone Repository:**
```bash
git clone https://github.com/RonyRisvaldoLumbanBatu/CBT-Rony.git
cd CBT-Rony
```

**2. Install Dependensi PHP (Composer) & Node (NPM):**
```bash
composer install
npm install
```

**3. Konfigurasi Environment:**
Salin file `.env.example` menjadi `.env`, lalu buat *App Key*.
```bash
cp .env.example .env
php artisan key:generate
```

Ubah pengaturan database di file `.env` Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```

**4. Migrasi Database dan Seeder (Akun Bawaan):**
Pastikan database sudah Anda buat di MySQL/XAMPP.
```bash
php artisan migrate:fresh --seed
```

**5. Kompilasi Aset Frontend (Tailwind/Vite):**
```bash
npm run build
```

**6. Jalankan Server Lokal Laravel:**
```bash
php artisan serve
```
Kunjungi `http://localhost:8000` di Web Browser Anda!

---

## 🔒 Akses Default (Setelah Seeder)

Jika Anda menjalankan langkah `migrate --seed`, Anda dapat langsung login menggunakan akun uji coba di bawah ini (Password untuk semua akun adalah: `password`):

| Role       | Email                      | Password   |
| ---------- | -------------------------- | ---------- |
| **Guru**   | `teacher@example.com`      | `password` |
| **Pengawas**| `proctor@example.com`     | `password` |
| **Siswa**  | `student@example.com`      | `password` |

*(Nb: Anda tentu dapat membuat / register akun baru langsung dari halaman depan pendaftaran. Anda juga dapat menggunakan perintah `php artisan migrate:fresh --seed` bawaan Laravel ini untuk me-reset ulang basis data).*

---

> **Dibuat dengan 🔥 oleh Rony Risvaldo Lumban Batu** <br>
> Jangan Ragu untuk memberikan *Star* (⭐) pada Repositori ini jika terbukti membantu proses belajar kita semua!
