<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['user_id', 'exam_id', 'score', 'answers_data', 'essay_scores', 'is_essay_graded'];

    protected function casts(): array
    {
        return [
            'answers_data' => 'array',
            'essay_scores' => 'array',
            'is_essay_graded' => 'boolean',
        ];
    }

    // Relasi: Nilai ini milik ujian yang mana?
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}