<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private User $guruA;

    private User $guruB;

    private Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guruA = User::factory()->create(['role' => 'guru']);
        $this->guruB = User::factory()->create(['role' => 'guru']);

        $this->exam = Exam::create([
            'teacher_id' => $this->guruA->id,
            'title' => 'Ujian Milik Guru A',
            'time_limit' => 60,
        ]);
    }

    public function test_guru_lain_tidak_bisa_membuka_halaman_ujian_milik_guru_a(): void
    {
        foreach (['soal', 'nilai', 'analisis', 'export', 'export-excel'] as $halaman) {
            $this->actingAs($this->guruB)
                ->get("/guru/ujian/{$this->exam->id}/{$halaman}")
                ->assertForbidden();
        }

        $this->actingAs($this->guruB)
            ->get("/pengawas/{$this->exam->id}")
            ->assertForbidden();
    }

    public function test_pemilik_ujian_bisa_membuka_halaman_ujiannya(): void
    {
        $this->actingAs($this->guruA)
            ->get("/guru/ujian/{$this->exam->id}/soal")
            ->assertOk();
    }

    public function test_ujian_lama_tanpa_pemilik_bisa_diakses_semua_guru(): void
    {
        $legacy = Exam::create(['title' => 'Ujian Lama Tanpa Pemilik', 'time_limit' => 60]);

        $this->actingAs($this->guruB)
            ->get("/guru/ujian/{$legacy->id}/soal")
            ->assertOk();
    }

    public function test_dashboard_guru_hanya_menampilkan_ujian_miliknya(): void
    {
        Exam::create([
            'teacher_id' => $this->guruB->id,
            'title' => 'Ujian Milik Guru B',
            'time_limit' => 30,
        ]);

        $this->actingAs($this->guruB)
            ->get('/guru/dashboard')
            ->assertSee('Ujian Milik Guru B')
            ->assertDontSee('Ujian Milik Guru A');
    }
}
