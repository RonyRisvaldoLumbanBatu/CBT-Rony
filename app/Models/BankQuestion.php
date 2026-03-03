<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankQuestion extends Model
{
    protected $fillable = [
        'question_bank_id',
        'type',
        'question_text',
    ];

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function options()
    {
        return $this->hasMany(BankOption::class, 'bank_question_id');
    }
}
