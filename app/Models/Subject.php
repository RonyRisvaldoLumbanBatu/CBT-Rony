<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = ['name'];

    /**
     * Relasi: ujian-ujian pada mata pelajaran ini.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
