<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// 1. Redirector Utama (Polisi Lalu Lintas)
Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru' => redirect()->route('guru.dashboard'),
        default => redirect()->route('siswa.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// 1b. Panel Administrator (satu dashboard untuk semua administrasi)
Route::get('/admin/dashboard', \App\Livewire\AdminPanel::class)
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

// 2. Rute Khusus Guru
Route::get('/guru/dashboard', \App\Livewire\TeacherDashboard::class)
    ->middleware(['auth', 'verified', 'role:guru'])
    ->name('guru.dashboard');

// 3. Rute Khusus Siswa (Pindahkan logika Leaderboard & Riwayat kemarin ke sini)
Route::get('/siswa/dashboard', function () {
    // essay_count dipakai view untuk menampilkan badge "menunggu penilaian essay"
    $riwayatNilai = \App\Models\Result::with(['exam' => function ($query) {
        $query->withCount(['questions as essay_count' => fn ($q) => $q->where('type', 'essay')]);
    }])->where('user_id', auth()->id())->latest()->get();

    // Hanya ujian untuk kelas siswa ini (atau ujian umum tanpa target kelas)
    $ujianTersedia = \App\Models\Exam::with('classroom')
        ->where('is_active', true)
        ->visibleTo(auth()->user())
        ->whereNotIn('id', $riwayatNilai->pluck('exam_id'))->get();

    $leaderboard = \App\Models\Result::with(['user.classroom', 'exam'])
        ->orderByDesc('score')->orderBy('created_at')->take(10)->get();

    return view('dashboard', compact('riwayatNilai', 'ujianTersedia', 'leaderboard'));
})->middleware(['auth', 'verified'])->name('siswa.dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

use App\Livewire\TakeExam;

// Rute ini digembok (middleware auth), wajib login dulu baru bisa ikut ujian
Route::get('/ujian/{id}', TakeExam::class)->middleware('auth');

// Rute untuk layar monitor Guru
Route::get('/pengawas/{id}', \App\Livewire\ProctorDashboard::class)
    ->middleware(['auth', 'role:guru']);

// Panel Admin Khusus Guru
Route::get('/guru/ujian', \App\Livewire\TeacherDashboard::class)->middleware(['auth', 'role:guru']);

// Kelola kelas, jurusan, akun, dan pengaturan aplikasi sekarang
// terpusat di Panel Administrator (/admin/dashboard).

// Cetak kartu peserta per kelas (Khusus Admin)
Route::get('/admin/kelas/{id}/kartu-peserta', [\App\Http\Controllers\PrintController::class, 'participantCards'])
    ->middleware(['auth', 'role:admin'])->name('admin.kartu');

// Cetak daftar hadir & berita acara ujian (Khusus Guru)
Route::get('/guru/ujian/{id}/daftar-hadir', [\App\Http\Controllers\PrintController::class, 'attendanceList'])
    ->middleware(['auth', 'role:guru']);
Route::get('/guru/ujian/{id}/berita-acara', [\App\Http\Controllers\PrintController::class, 'officialReport'])
    ->middleware(['auth', 'role:guru']);

// Halaman untuk kelola soal (Khusus Guru)
Route::get('/guru/ujian/{id}/soal', \App\Livewire\ManageQuestions::class)->middleware(['auth', 'role:guru']);

// Halaman untuk kelola nilai dan essay (Khusus Guru)
Route::get('/guru/ujian/{id}/nilai', \App\Livewire\GradeExam::class)->middleware(['auth', 'role:guru']);

// Halaman untuk analisis kesukaran butir soal (Khusus Guru)
Route::get('/guru/ujian/{id}/analisis', \App\Livewire\ItemAnalysis::class)->middleware(['auth', 'role:guru']);

// Rute Export PDF (Khusus Guru)
Route::get('/guru/ujian/{id}/export', [\App\Http\Controllers\ExportController::class, 'exportPdf'])->middleware(['auth', 'role:guru']);

// Rute Export Excel/CSV (Khusus Guru)
Route::get('/guru/ujian/{id}/export-excel', [\App\Http\Controllers\ExportController::class, 'exportExcel'])->middleware(['auth', 'role:guru']);

require __DIR__.'/auth.php';
