<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kelas / rombongan belajar (sekolah) atau kelas mata kuliah (kampus)
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Pengaturan aplikasi key-value (dipakai untuk mode sekolah/kampus)
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value');
            $table->timestamps();
        });

        // Siswa tergabung ke SATU kelas
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('classroom_id')
                ->nullable()
                ->after('role')
                ->constrained()
                ->nullOnDelete();
        });

        // Ujian bisa ditargetkan ke satu kelas; null = semua kelas
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('classroom_id')
                ->nullable()
                ->after('teacher_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('classroom_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('classroom_id');
        });

        Schema::dropIfExists('settings');
        Schema::dropIfExists('classrooms');
    }
};
