<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Jejak status pengerjaan ujian per peserta.
 * Dipakai radar pengawas: siapa yang sedang mengerjakan, sampai soal
 * berapa, berapa pelanggaran, dan siapa yang sudah selesai.
 */
class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'current_index',
        'answered_count',
        'total_questions',
        'strikes',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'finished_at' => 'datetime',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
