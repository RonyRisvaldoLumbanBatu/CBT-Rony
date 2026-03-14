<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'answers_data',
        'essay_scores',
        'is_essay_graded',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'answers_data' => 'array',
            'essay_scores' => 'array',
            'is_essay_graded' => 'boolean',
        ];
    }

    /**
     * Relasi: Nilai ini milik ujian yang mana?
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Relasi: Nilai ini milik user yang mana?
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
