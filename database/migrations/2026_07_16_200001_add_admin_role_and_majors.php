<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom role semula enum('guru','siswa'). Di PostgreSQL enum Laravel
        // dibuat sebagai varchar + CHECK constraint, jadi constraint lamanya
        // harus dilepas dulu sebelum role 'admin' bisa disimpan.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('siswa')->change();
        });

        // Jurusan (sekolah: IPA/IPS/TKJ..., kampus: program studi)
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Satu kelas tergabung ke satu jurusan (opsional)
        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('major_id')
                ->nullable()
                ->after('name')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('major_id');
        });

        Schema::dropIfExists('majors');
    }
};
