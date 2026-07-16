<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'teacher_id',
        'classroom_id',
        'title',
        'description',
        'time_limit',
        'is_active',
        'token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi: Ujian ini dibuat oleh guru siapa?
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Relasi: Ujian ini ditargetkan ke kelas mana? (null = semua kelas)
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Apakah siswa ini boleh mengikuti ujian?
     * Ujian tanpa kelas (null) terbuka untuk semua siswa.
     */
    public function isOpenFor(User $student): bool
    {
        return $this->classroom_id === null || $this->classroom_id === $student->classroom_id;
    }

    /**
     * Scope: ujian yang terlihat oleh siswa tertentu (sesuai kelasnya).
     */
    public function scopeVisibleTo(Builder $query, User $student): Builder
    {
        return $query->where(fn ($q) => $q->whereNull('classroom_id')
            ->orWhere('classroom_id', $student->classroom_id));
    }

    /**
     * Guru hanya boleh mengelola ujiannya sendiri.
     * teacher_id null = ujian lama (sebelum fitur kepemilikan), boleh diakses semua guru.
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->teacher_id === null || $this->teacher_id === $user->id;
    }

    /**
     * Scope: ujian milik guru tertentu (termasuk ujian lama tanpa pemilik).
     */
    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where(fn ($q) => $q->where('teacher_id', $user->id)->orWhereNull('teacher_id'));
    }

    /**
     * Relasi: Satu ujian punya BANYAK soal.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Relasi: Satu ujian punya BANYAK hasil (result) pengerjaan.
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}
