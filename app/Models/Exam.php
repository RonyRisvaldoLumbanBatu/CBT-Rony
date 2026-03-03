<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    // Tambahkan 'is_active' dan 'token' di dalam array $fillable
    protected $fillable = ['title', 'description', 'time_limit', 'is_active', 'token'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // 2. Relasi: Satu ujian punya BANYAK soal
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}