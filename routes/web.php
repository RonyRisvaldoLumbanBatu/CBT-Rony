<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// 1. Redirector Utama (Polisi Lalu Lintas)
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'guru') {
        return redirect()->route('guru.dashboard');
    }
    return redirect()->route('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 2. Rute Khusus Guru
Route::get('/guru/dashboard', \App\Livewire\TeacherDashboard::class)
    ->middleware(['auth', 'verified', 'role:guru'])
    ->name('guru.dashboard');

// 3. Rute Khusus Siswa (Pindahkan logika Leaderboard & Riwayat kemarin ke sini)
Route::get('/siswa/dashboard', function () {
    $riwayatNilai = \App\Models\Result::with('exam')->where('user_id', auth()->id())->latest()->get();

    $ujianTersedia = \App\Models\Exam::where('is_active', true)
        ->whereNotIn('id', $riwayatNilai->pluck('exam_id'))->get();

    $leaderboard = \App\Models\Result::with(['user', 'exam'])
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

// Halaman untuk kelola soal (Khusus Guru)
Route::get('/guru/ujian/{id}/soal', \App\Livewire\ManageQuestions::class)->middleware(['auth', 'role:guru']);

// Rute Export PDF (Khusus Guru)
Route::get('/guru/ujian/{id}/export', [\App\Http\Controllers\ExportController::class, 'exportPdf'])->middleware(['auth', 'role:guru']);

// Rute Export Excel/CSV (Khusus Guru)
Route::get('/guru/ujian/{id}/export-excel', [\App\Http\Controllers\ExportController::class, 'exportExcel'])->middleware(['auth', 'role:guru']);

require __DIR__ . '/auth.php';
