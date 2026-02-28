<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 1 Akun Guru/Admin agar kita bisa login nanti
        User::factory()->create([
            'name' => 'Pak Guru IT',
            'email' => 'guru@ujian.com',
            'password' => bcrypt('password123'), // Ingat password ini ya!
        ]);

        // 2. Buat 1 Data Ujian
        $ujian = Exam::create([
            'title' => 'Ujian Tengah Semester: Pemrograman Laravel 12',
            'description' => 'Ujian ini menguji pemahaman tentang MVC, Eloquent, dan Livewire.',
            'time_limit' => 60, // Waktu ujian 60 Menit
        ]);

        // 3. Buat Soal Nomor 1
        $soal1 = Question::create([
            'exam_id' => $ujian->id,
            'question_text' => 'Apa kepanjangan dari PHP pada saat pertama kali diciptakan?'
        ]);
        Option::create(['question_id' => $soal1->id, 'option_text' => 'Pre-Hypertext Processor', 'is_correct' => false]);
        Option::create(['question_id' => $soal1->id, 'option_text' => 'Personal Home Page', 'is_correct' => true]); // Jawaban Benar
        Option::create(['question_id' => $soal1->id, 'option_text' => 'Private Hosting Platform', 'is_correct' => false]);
        Option::create(['question_id' => $soal1->id, 'option_text' => 'Programming Hypertext Protocol', 'is_correct' => false]);

        // 4. Buat Soal Nomor 2
        $soal2 = Question::create([
            'exam_id' => $ujian->id,
            'question_text' => 'Manakah di bawah ini yang merupakan fitur ajaib Laravel untuk membuat web reaktif tanpa JavaScript?'
        ]);
        Option::create(['question_id' => $soal2->id, 'option_text' => 'Laravel Sanctum', 'is_correct' => false]);
        Option::create(['question_id' => $soal2->id, 'option_text' => 'Laravel Eloquent', 'is_correct' => false]);
        Option::create(['question_id' => $soal2->id, 'option_text' => 'Laravel Livewire', 'is_correct' => true]); // Jawaban Benar
        Option::create(['question_id' => $soal2->id, 'option_text' => 'Laravel Reverb', 'is_correct' => false]);
    }
}