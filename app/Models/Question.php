<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['exam_id', 'question_text', 'type'];

    // Relasi ke Atas: Soal ini MILIK ujian apa?
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Relasi ke Bawah: Soal ini PUNYA BANYAK pilihan jawaban
    public function options()
    {
        return $this->hasMany(Option::class);
    }
}