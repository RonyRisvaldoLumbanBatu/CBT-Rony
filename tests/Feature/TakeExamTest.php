<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use App\Models\Result;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TakeExamTest extends TestCase
{
    use RefreshDatabase;

    private User $siswa;

    private Exam $exam;

    private Question $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siswa = User::factory()->create(['role' => 'siswa']);

        $this->exam = Exam::create([
            'title' => 'Ujian Feature Test',
            'description' => 'Testing alur ujian',
            'time_limit' => 60,
            'is_active' => true,
        ]);

        $this->question = Question::create([
            'exam_id' => $this->exam->id,
            'question_text' => '1 + 1 = ?',
            'type' => 'pg',
        ]);
        Option::create(['question_id' => $this->question->id, 'option_text' => '2', 'is_correct' => true]);
        Option::create(['question_id' => $this->question->id, 'option_text' => '3', 'is_correct' => false]);
    }

    public function test_ujian_draft_tidak_bisa_diakses_lewat_url_langsung(): void
    {
        $this->exam->update(['is_active' => false, 'token' => null]);

        $this->actingAs($this->siswa)
            ->get('/ujian/'.$this->exam->id)
            ->assertRedirect(route('dashboard'));
    }

    public function test_siswa_yang_sudah_mengerjakan_tidak_bisa_mengulang(): void
    {
        Result::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'score' => 80,
            'answers_data' => [],
        ]);

        $this->actingAs($this->siswa)
            ->get('/ujian/'.$this->exam->id)
            ->assertRedirect(route('dashboard'));
    }

    public function test_submit_menyimpan_nilai_yang_benar(): void
    {
        $kunci = $this->question->options()->where('is_correct', true)->first();

        Livewire::actingAs($this->siswa)
            ->test(\App\Livewire\TakeExam::class, ['id' => $this->exam->id])
            ->set('answers.'.$this->question->id, (string) $kunci->id)
            ->call('submitExam')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('results', [
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'score' => 100,
        ]);
    }

    public function test_double_submit_tidak_membuat_nilai_ganda(): void
    {
        $component = Livewire::actingAs($this->siswa)
            ->test(\App\Livewire\TakeExam::class, ['id' => $this->exam->id]);

        $component->call('submitExam');
        $component->call('submitExam'); // simulasi timer habis + klik submit bersamaan

        $this->assertSame(1, Result::where('user_id', $this->siswa->id)
            ->where('exam_id', $this->exam->id)
            ->count());
    }
}
