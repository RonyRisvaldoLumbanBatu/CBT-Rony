<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankOption extends Model
{
    protected $fillable = [
        'bank_question_id',
        'option_text',
        'is_correct',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function bankQuestion()
    {
        return $this->belongsTo(BankQuestion::class, 'bank_question_id');
    }
}
