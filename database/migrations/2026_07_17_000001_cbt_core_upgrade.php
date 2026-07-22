<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Identitas peserta ala CBT: login dengan username, punya nomor induk.
        // plain_password disimpan agar kartu peserta bisa dicetak (konvensi
        // aplikasi CBT sekolah seperti BeeSmart/Garuda).
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');
            $table->string('nis', 30)->nullable()->after('username');
            $table->string('plain_password', 100)->nullable()->after('password');
        });

        // Mata pelajaran / mata kuliah
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('classroom_id')->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('kkm')->default(70)->after('time_limit');
            // Jadwal opsional: jika diisi, ujian hanya bisa diakses dalam rentang ini
            $table->timestamp('starts_at')->nullable()->after('kkm');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
            // Rilis nilai: false = nilai ditahan guru, siswa belum bisa melihat
            $table->boolean('results_released')->default(true)->after('ends_at');
        });

        // Bobot poin per soal (default 1 = perilaku lama, semua soal setara)
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedSmallInteger('points')->default(1)->after('type');
        });

        // Status pengerjaan per peserta untuk radar pengawas
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('current_index')->default(0);
            $table->unsignedSmallInteger('answered_count')->default(0);
            $table->unsignedSmallInteger('total_questions')->default(0);
            $table->unsignedSmallInteger('strikes')->default(0);
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->unique(['exam_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('points');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subject_id');
            $table->dropColumn(['kkm', 'starts_at', 'ends_at', 'results_released']);
        });

        Schema::dropIfExists('subjects');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'nis', 'plain_password']);
        });
    }
};
