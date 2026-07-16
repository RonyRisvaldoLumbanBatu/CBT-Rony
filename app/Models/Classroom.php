<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    protected $fillable = ['name'];

    /**
     * Relasi: siswa/mahasiswa yang tergabung di kelas ini.
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'siswa');
    }

    /**
     * Relasi: ujian yang ditargetkan khusus ke kelas ini.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
