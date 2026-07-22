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
        'subject_id',
        'title',
        'description',
        'time_limit',
        'kkm',
        'starts_at',
        'ends_at',
        'results_released',
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
            'results_released' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
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
     * Relasi: Mata pelajaran ujian ini.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relasi: status pengerjaan peserta (untuk radar pengawas).
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Apakah ujian bisa dikerjakan SEKARANG?
     * - Harus diaktifkan guru (is_active).
     * - Jika jadwal diisi, waktu sekarang harus berada dalam rentangnya.
     */
    public function isOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && now()->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * Ujian aktif yang jadwal mulainya masih di masa depan.
     */
    public function isUpcoming(): bool
    {
        return $this->is_active && $this->starts_at && now()->lt($this->starts_at);
    }

    /**
     * Scope: ujian yang bisa dikerjakan sekarang (aktif + dalam jadwal).
     */
    public function scopeOpenNow(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
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
