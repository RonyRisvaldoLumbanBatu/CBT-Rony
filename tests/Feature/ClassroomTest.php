<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassroomTest extends TestCase
{
    use RefreshDatabase;

    private Classroom $kelasA;

    private Classroom $kelasB;

    private User $siswaA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kelasA = Classroom::create(['name' => 'XII IPA 1']);
        $this->kelasB = Classroom::create(['name' => 'XII IPA 2']);

        $this->siswaA = User::factory()->create([
            'role' => 'siswa',
            'classroom_id' => $this->kelasA->id,
        ]);
    }

    private function buatUjian(?int $classroomId, string $title = 'Ujian Test'): Exam
    {
        $exam = Exam::create([
            'title' => $title,
            'time_limit' => 60,
            'is_active' => true,
            'classroom_id' => $classroomId,
        ]);

        $question = \App\Models\Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'Soal contoh',
            'type' => 'pg',
        ]);
        \App\Models\Option::create(['question_id' => $question->id, 'option_text' => 'A', 'is_correct' => true]);
        \App\Models\Option::create(['question_id' => $question->id, 'option_text' => 'B', 'is_correct' => false]);

        return $exam;
    }

    public function test_siswa_hanya_melihat_ujian_kelasnya_dan_ujian_umum(): void
    {
        $this->buatUjian($this->kelasA->id, 'Ujian Kelas A');
        $this->buatUjian($this->kelasB->id, 'Ujian Kelas B');
        $this->buatUjian(null, 'Ujian Untuk Semua');

        $this->actingAs($this->siswaA)
            ->get('/siswa/dashboard')
            ->assertSee('Ujian Kelas A')
            ->assertSee('Ujian Untuk Semua')
            ->assertDontSee('Ujian Kelas B');
    }

    public function test_siswa_kelas_lain_ditolak_dari_ujian_bertarget_kelas(): void
    {
        $ujianKelasB = $this->buatUjian($this->kelasB->id);

        $this->actingAs($this->siswaA)
            ->get('/ujian/'.$ujianKelasB->id)
            ->assertRedirect(route('dashboard'));
    }

    public function test_siswa_boleh_mengikuti_ujian_kelasnya_sendiri(): void
    {
        $ujianKelasA = $this->buatUjian($this->kelasA->id);

        $this->actingAs($this->siswaA)
            ->get('/ujian/'.$ujianKelasA->id)
            ->assertOk();
    }

    public function test_mode_kampus_mengubah_istilah(): void
    {
        $this->assertSame('Siswa', term('siswa'));
        $this->assertSame('Guru', term('guru'));

        Setting::set('institution_mode', 'kampus');

        $this->assertSame('Mahasiswa', term('siswa'));
        $this->assertSame('Dosen', term('guru'));
        $this->assertSame('Kampus', term('sekolah'));
    }

    public function test_admin_bisa_membuat_dan_menghapus_kelas(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminPanel::class)
            ->set('c_name', 'XI IPS 3')
            ->call('createClassroom');

        $this->assertDatabaseHas('classrooms', ['name' => 'XI IPS 3']);

        $kelas = Classroom::where('name', 'XI IPS 3')->first();

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminPanel::class)
            ->call('deleteClassroom', $kelas->id);

        $this->assertDatabaseMissing('classrooms', ['name' => 'XI IPS 3']);
    }
}
