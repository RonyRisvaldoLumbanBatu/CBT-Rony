<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['user_id', 'exam_id', 'score', 'answers_data'];

    protected $casts = [
        'answers_data' => 'array',
    ];

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