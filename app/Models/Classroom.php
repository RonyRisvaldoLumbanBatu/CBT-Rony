<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    protected $fillable = ['name', 'major_id'];

    /**
     * Relasi: jurusan tempat kelas ini bernaung (opsional).
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

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
