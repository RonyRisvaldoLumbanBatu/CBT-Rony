<?php

namespace Tests\Feature;

use App\Livewire\AdminPanel;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Major;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_hanya_admin_yang_bisa_membuka_panel_admin(): void
    {
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk();

        $guru = User::factory()->create(['role' => 'guru']);
        $this->actingAs($guru)->get('/admin/dashboard')->assertForbidden();

        $siswa = User::factory()->create(['role' => 'siswa']);
        $this->actingAs($siswa)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_dashboard_redirector_mengarahkan_admin_ke_panel(): void
    {
        $this->actingAs($this->admin)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_bisa_membuat_akun_siswa_dengan_kelas(): void
    {
        $kelas = Classroom::create(['name' => 'XII IPA 1']);

        Livewire::actingAs($this->admin)
            ->test(AdminPanel::class)
            ->set('u_name', 'Budi Santoso')
            ->set('u_email', 'budi@sekolah.sch.id')
            ->set('u_password', 'rahasia123')
            ->set('u_role', 'siswa')
            ->set('u_classroom_id', (string) $kelas->id)
            ->call('saveUser');

        $this->assertDatabaseHas('users', [
            'email' => 'budi@sekolah.sch.id',
            'role' => 'siswa',
            'classroom_id' => $kelas->id,
        ]);

        // Akun buatan admin langsung bisa dipakai login
        $budi = User::where('email', 'budi@sekolah.sch.id')->first();
        $this->assertTrue(Hash::check('rahasia123', $budi->password));
        $this->assertNotNull($budi->email_verified_at);
    }

    public function test_admin_bisa_membuat_akun_guru(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminPanel::class)
            ->set('u_name', 'Bu Dosen')
            ->set('u_email', 'dosen@kampus.ac.id')
            ->set('u_password', 'rahasia123')
            ->set('u_role', 'guru')
            ->call('saveUser');

        $this->assertDatabaseHas('users', [
            'email' => 'dosen@kampus.ac.id',
            'role' => 'guru',
        ]);
    }

    public function test_password_wajib_saat_buat_akun_baru(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminPanel::class)
            ->set('u_name', 'Tanpa Password')
            ->set('u_email', 'tanpa@pass.com')
            ->set('u_role', 'siswa')
            ->call('saveUser')
            ->assertHasErrors(['u_password' => 'required']);
    }

    public function test_admin_bisa_reset_password_lewat_edit(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        Livewire::actingAs($this->admin)
            ->test(AdminPanel::class)
            ->call('editUser', $siswa->id)
            ->set('u_password', 'passwordbaru1')
            ->call('saveUser');

        $this->assertTrue(Hash::check('passwordbaru1', $siswa->fresh()->password));
    }

    public function test_admin_tidak_bisa_menghapus_akunnya_sendiri(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminPanel::class)
            ->call('deleteUser', $this->admin->id);

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_bisa_mengelola_jurusan_dan_menautkannya_ke_kelas(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminPanel::class)
            ->set('m_name', 'TKJ')
            ->call('createMajor');

        $major = Major::where('name', 'TKJ')->first();
        $this->assertNotNull($major);

        Livewire::actingAs($this->admin)
            ->test(AdminPanel::class)
            ->set('c_name', 'XI TKJ 1')
            ->set('c_major_id', (string) $major->id)
            ->call('createClassroom');

        $this->assertDatabaseHas('classrooms', ['name' => 'XI TKJ 1', 'major_id' => $major->id]);
    }

    public function test_admin_bisa_mengubah_nama_aplikasi(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminPanel::class)
            ->set('s_app_name', 'CBT Sekolahku')
            ->set('s_institution_name', 'SMA Negeri 1 Medan')
            ->set('s_academic_year', '2026/2027 Ganjil')
            ->call('saveSettings');

        $this->assertSame('CBT Sekolahku', cbt_name());
        $this->assertSame('SMA Negeri 1 Medan', cbt_institution());

        // Branding tampil di halaman depan
        $this->get('/')->assertSee('CBT Sekolahku')->assertSee('SMA Negeri 1 Medan');
    }

    public function test_monitoring_menampilkan_ujian_yang_berlangsung(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'name' => 'Pak Budi']);
        Exam::create([
            'teacher_id' => $guru->id,
            'title' => 'UTS Matematika Berlangsung',
            'time_limit' => 90,
            'is_active' => true,
            'token' => 'ABC123',
        ]);
        Exam::create(['title' => 'Ujian Draft', 'time_limit' => 60, 'is_active' => false]);

        $this->actingAs($this->admin)
            ->get('/admin/dashboard')
            ->assertSee('UTS Matematika Berlangsung')
            ->assertSee('Pak Budi')
            ->assertDontSee('Ujian Draft');
    }
}
