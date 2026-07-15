<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            // Satu siswa hanya boleh punya SATU hasil per ujian.
            // Mencegah nilai ganda saat double-submit (timer habis + klik submit bersamaan).
            $table->unique(['user_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'exam_id']);
        });
    }
};
