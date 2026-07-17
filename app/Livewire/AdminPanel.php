<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Major;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Panel Administrator: satu dashboard untuk seluruh administrasi CBT.
 * Tab: ringkasan (monitoring ujian), pengguna, kelas & jurusan, pengaturan aplikasi.
 */
#[Layout('layouts.app')]
class AdminPanel extends Component
{
    use WithFileUploads;

    public $tab = 'ringkasan';

    // ===== Tab Pengguna =====
    public $userSearch = '';

    public $roleFilter = 'semua';

    public $isUserModalOpen = false;

    public $editingUserId = null;

    public $u_name = '';

    public $u_email = '';

    public $u_password = '';

    public $u_role = 'siswa';

    public $u_classroom_id = '';

    // ===== Tab Kelas & Jurusan =====
    public $m_name = '';

    public $c_name = '';

    public $c_major_id = '';

    public $editingClassroomId = null;

    public $editingClassroomName = '';

    public $editingClassroomMajorId = '';

    // ===== Tab Pengaturan =====
    public $s_app_name = '';

    public $s_institution_name = '';

    public $s_academic_year = '';

    public $s_logo; // upload

    public $s_banner; // upload

    public function mount()
    {
        $this->s_app_name = app_setting('app_name', 'UjianPintar');
        $this->s_institution_name = app_setting('institution_name', '');
        $this->s_academic_year = app_setting('academic_year', '');
    }

    public function setTab($tab)
    {
        if (in_array($tab, ['ringkasan', 'pengguna', 'kelas', 'pengaturan'])) {
            $this->tab = $tab;
        }
    }

    // =====================================================================
    // PENGGUNA (akun siswa & guru dibuat oleh admin, tidak ada pendaftaran)
    // =====================================================================

    public function openUserModal()
    {
        $this->resetUserForm();
        $this->isUserModalOpen = true;
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->u_name = $user->name;
        $this->u_email = $user->email;
        $this->u_role = $user->role;
        $this->u_classroom_id = $user->classroom_id ?? '';
        $this->u_password = '';
        $this->isUserModalOpen = true;
    }

    public function saveUser()
    {
        $rules = [
            'u_name' => 'required|string|max:255',
            'u_email' => 'required|email|max:255|unique:users,email'.($this->editingUserId ? ','.$this->editingUserId : ''),
            'u_role' => 'required|in:siswa,guru',
            'u_classroom_id' => 'nullable|exists:classrooms,id',
            // Password wajib saat buat akun baru; opsional saat edit (isi = ganti)
            'u_password' => $this->editingUserId ? 'nullable|string|min:8' : 'required|string|min:8',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->u_name,
            'email' => strtolower($this->u_email),
            'role' => $this->u_role,
            'classroom_id' => $this->u_role === 'siswa' ? ($this->u_classroom_id ?: null) : null,
        ];

        if ($this->u_password) {
            $data['password'] = Hash::make($this->u_password);
        }

        if ($this->editingUserId) {
            User::findOrFail($this->editingUserId)->update($data);
            session()->flash('sukses', 'Akun berhasil diperbarui!');
        } else {
            $user = new User($data);
            // Akun buatan admin langsung terverifikasi (tidak perlu klik email)
            $user->email_verified_at = now();
            $user->save();
            session()->flash('sukses', 'Akun '.term($this->u_role).' baru berhasil dibuat!');
        }

        $this->closeUserModal();
    }

    public function deleteUser($id)
    {
        if ((int) $id === auth()->id()) {
            session()->flash('gagal', 'Kamu tidak bisa menghapus akunmu sendiri.');

            return;
        }

        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            session()->flash('gagal', 'Akun administrator tidak bisa dihapus dari sini.');

            return;
        }

        $user->delete();
        session()->flash('sukses', 'Akun berhasil dihapus.');
    }

    public function closeUserModal()
    {
        $this->isUserModalOpen = false;
        $this->resetUserForm();
    }

    private function resetUserForm()
    {
        $this->reset(['editingUserId', 'u_name', 'u_email', 'u_password', 'u_classroom_id']);
        $this->u_role = 'siswa';
        $this->resetErrorBag();
    }

    // =====================================================================
    // KELAS & JURUSAN
    // =====================================================================

    public function createMajor()
    {
        $this->validate(['m_name' => 'required|min:2|max:50|unique:majors,name']);
        Major::create(['name' => $this->m_name]);
        $this->reset('m_name');
        session()->flash('sukses', 'Jurusan baru berhasil dibuat!');
    }

    public function deleteMajor($id)
    {
        Major::findOrFail($id)->delete();
        session()->flash('sukses', 'Jurusan dihapus. Kelas anggotanya menjadi tanpa jurusan.');
    }

    public function createClassroom()
    {
        $this->validate([
            'c_name' => 'required|min:2|max:50|unique:classrooms,name',
            'c_major_id' => 'nullable|exists:majors,id',
        ]);

        Classroom::create(['name' => $this->c_name, 'major_id' => $this->c_major_id ?: null]);
        $this->reset('c_name', 'c_major_id');
        session()->flash('sukses', term('kelas').' baru berhasil dibuat!');
    }

    public function startEditClassroom($id)
    {
        $classroom = Classroom::findOrFail($id);
        $this->editingClassroomId = $classroom->id;
        $this->editingClassroomName = $classroom->name;
        $this->editingClassroomMajorId = $classroom->major_id ?? '';
    }

    public function saveClassroom()
    {
        $this->validate([
            'editingClassroomName' => 'required|min:2|max:50|unique:classrooms,name,'.$this->editingClassroomId,
            'editingClassroomMajorId' => 'nullable|exists:majors,id',
        ]);

        Classroom::findOrFail($this->editingClassroomId)->update([
            'name' => $this->editingClassroomName,
            'major_id' => $this->editingClassroomMajorId ?: null,
        ]);

        $this->reset('editingClassroomId', 'editingClassroomName', 'editingClassroomMajorId');
        session()->flash('sukses', 'Data '.strtolower(term('kelas')).' berhasil diubah!');
    }

    public function cancelEditClassroom()
    {
        $this->reset('editingClassroomId', 'editingClassroomName', 'editingClassroomMajorId');
    }

    public function deleteClassroom($id)
    {
        // FK nullOnDelete: siswa & ujian kelas ini otomatis jadi "tanpa kelas"
        Classroom::findOrFail($id)->delete();
        session()->flash('sukses', term('kelas').' berhasil dihapus. Anggotanya menjadi tanpa '.strtolower(term('kelas')).'.');
    }

    // =====================================================================
    // PENGATURAN APLIKASI
    // =====================================================================

    public function setMode($mode)
    {
        if (! in_array($mode, ['sekolah', 'kampus'])) {
            return;
        }

        Setting::set('institution_mode', $mode);
        session()->flash('sukses', 'Mode aplikasi diubah ke: '.ucfirst($mode).'.');
    }

    public function saveSettings()
    {
        $this->validate([
            's_app_name' => 'required|string|min:2|max:40',
            's_institution_name' => 'nullable|string|max:80',
            's_academic_year' => 'nullable|string|max:30',
            's_logo' => 'nullable|image|max:1024',
            's_banner' => 'nullable|image|max:2048',
        ]);

        Setting::set('app_name', $this->s_app_name);
        Setting::set('institution_name', $this->s_institution_name ?? '');
        Setting::set('academic_year', $this->s_academic_year ?? '');

        if ($this->s_logo) {
            $this->replaceUploadedSetting('logo_path', $this->s_logo->store('branding', 'public'));
            $this->reset('s_logo');
        }

        if ($this->s_banner) {
            $this->replaceUploadedSetting('banner_path', $this->s_banner->store('branding', 'public'));
            $this->reset('s_banner');
        }

        session()->flash('sukses', 'Pengaturan aplikasi berhasil disimpan!');
    }

    public function removeLogo()
    {
        $this->replaceUploadedSetting('logo_path', '');
        session()->flash('sukses', 'Logo dihapus, kembali ke ikon bawaan.');
    }

    public function removeBanner()
    {
        $this->replaceUploadedSetting('banner_path', '');
        session()->flash('sukses', 'Banner dihapus.');
    }

    private function replaceUploadedSetting(string $key, string $newPath): void
    {
        $old = app_setting($key);
        if ($old) {
            Storage::disk('public')->delete($old);
        }
        Setting::set($key, $newPath);
    }

    // =====================================================================

    public function render()
    {
        // Monitoring: ujian yang sedang berlangsung + jumlah pengumpul
        $ujianBerlangsung = Exam::with(['teacher', 'classroom'])
            ->withCount('results')
            ->where('is_active', true)
            ->latest()
            ->get();

        $users = User::with('classroom.major')
            ->when($this->roleFilter !== 'semua', fn ($q) => $q->where('role', $this->roleFilter))
            ->when(strlen($this->userSearch) > 0, fn ($q) => $q->where(
                fn ($qq) => $qq->where('name', 'like', '%'.$this->userSearch.'%')
                    ->orWhere('email', 'like', '%'.$this->userSearch.'%')
            ))
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('livewire.admin-panel', [
            'ujianBerlangsung' => $ujianBerlangsung,
            'users' => $users,
            'classrooms' => Classroom::with('major')->withCount('students')->orderBy('name')->get(),
            'majors' => Major::withCount('classrooms')->orderBy('name')->get(),
            'stats' => [
                'siswa' => User::where('role', 'siswa')->count(),
                'guru' => User::where('role', 'guru')->count(),
                'kelas' => Classroom::count(),
                'ujianAktif' => Exam::where('is_active', true)->count(),
            ],
            'currentMode' => app_mode(),
        ]);
    }
}
