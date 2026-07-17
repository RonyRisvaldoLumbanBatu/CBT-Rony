<div class="max-w-7xl mx-auto py-6 sm:py-10 px-4 sm:px-6 lg:px-8 transition-colors duration-300">

    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white">Panel Administrator</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Kelola akun, {{ strtolower(term('kelas')) }}, jurusan, dan identitas aplikasi {{ cbt_name() }}.
            @if(app_setting('academic_year'))
                <span class="font-bold text-indigo-600 dark:text-indigo-400">Tahun Ajaran {{ app_setting('academic_year') }}</span>
            @endif
        </p>
    </div>

    {{-- Flash --}}
    @if(session('sukses'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-4 shadow-sm rounded-r-lg flex items-center gap-3">
            <x-icon name="check-circle" stroke="1.5" class="w-6 h-6 shrink-0 text-green-600 dark:text-green-400" />
            <span class="text-green-700 dark:text-green-400 font-medium text-sm">{{ session('sukses') }}</span>
        </div>
    @endif
    @if(session('gagal'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 shadow-sm rounded-r-lg flex items-center gap-3">
            <x-icon name="warning-triangle" stroke="1.5" class="w-6 h-6 shrink-0 text-red-600 dark:text-red-400" />
            <span class="text-red-700 dark:text-red-400 font-medium text-sm">{{ session('gagal') }}</span>
        </div>
    @endif

    {{-- Navigasi Tab --}}
    <div class="flex flex-wrap gap-2 mb-8 bg-white dark:bg-gray-800 p-1.5 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm w-fit">
        @foreach(['ringkasan' => 'Ringkasan & Monitoring', 'pengguna' => 'Manajemen Pengguna', 'kelas' => term('kelas').' & Jurusan', 'pengaturan' => 'Pengaturan Aplikasi'] as $key => $label)
            <button wire:click="setTab('{{ $key }}')"
                class="px-4 py-2 rounded-lg text-sm font-bold transition {{ $tab === $key ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ================= TAB: RINGKASAN & MONITORING ================= --}}
    @if($tab === 'ringkasan')
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @foreach([
                ['label' => 'Total '.term('siswa'), 'value' => $stats['siswa'], 'warna' => 'text-indigo-600 dark:text-indigo-400'],
                ['label' => 'Total '.term('guru'), 'value' => $stats['guru'], 'warna' => 'text-emerald-600 dark:text-emerald-400'],
                ['label' => 'Total '.term('kelas'), 'value' => $stats['kelas'], 'warna' => 'text-amber-600 dark:text-amber-400'],
                ['label' => 'Ujian Berlangsung', 'value' => $stats['ujianAktif'], 'warna' => 'text-rose-600 dark:text-rose-400'],
            ] as $stat)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <p class="text-3xl font-extrabold {{ $stat['warna'] }}">{{ $stat['value'] }}</p>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <x-icon name="signal" stroke="1.5" class="w-5 h-5 text-rose-500" />
                <h3 class="font-bold text-gray-800 dark:text-white">Ujian yang Sedang Berlangsung</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($ujianBerlangsung as $ujian)
                    <div class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 dark:text-gray-100 truncate">{{ $ujian->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ term('guru') }}: <span class="font-semibold">{{ $ujian->teacher?->name ?? '-' }}</span>
                                &middot; {{ $ujian->classroom?->name ?? 'Semua '.term('kelas') }}
                                &middot; {{ $ujian->time_limit }} Menit
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 px-3 py-1 rounded-md text-xs font-bold tracking-widest font-mono">PIN: {{ $ujian->token }}</span>
                            <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 px-3 py-1 rounded-md text-xs font-bold">{{ $ujian->results_count }} sudah mengumpulkan</span>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-gray-400 dark:text-gray-500 text-sm font-medium">
                        Tidak ada ujian yang sedang berlangsung saat ini.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ================= TAB: MANAJEMEN PENGGUNA ================= --}}
    @if($tab === 'pengguna')
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
            <div class="relative flex-1 sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="search" class="w-4 h-4 text-gray-400" />
                </div>
                <input type="text" wire:model.live="userSearch"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                    placeholder="Cari nama / email...">
            </div>
            <select wire:model.live="roleFilter"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="semua">Semua Role</option>
                <option value="siswa">{{ term('siswa') }}</option>
                <option value="guru">{{ term('guru') }}</option>
                <option value="admin">Administrator</option>
            </select>
            <button wire:click="openUserModal"
                class="sm:ml-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-sm transition">
                <x-icon name="plus" class="w-4 h-4" /> Buat Akun
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5 font-bold">Nama</th>
                            <th class="py-3.5 px-5 font-bold">Email</th>
                            <th class="py-3.5 px-5 font-bold">Role</th>
                            <th class="py-3.5 px-5 font-bold">{{ term('kelas') }} / Jurusan</th>
                            <th class="py-3.5 px-5 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="py-3.5 px-5 font-bold whitespace-nowrap">{{ $user->name }}</td>
                                <td class="py-3.5 px-5 text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="py-3.5 px-5">
                                    @if($user->role === 'admin')
                                        <span class="bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400 px-2.5 py-1 rounded-md text-xs font-bold uppercase">Admin</span>
                                    @elseif($user->role === 'guru')
                                        <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 px-2.5 py-1 rounded-md text-xs font-bold uppercase">{{ term('guru') }}</span>
                                    @else
                                        <span class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 px-2.5 py-1 rounded-md text-xs font-bold uppercase">{{ term('siswa') }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    @if($user->classroom)
                                        {{ $user->classroom->name }}
                                        @if($user->classroom->major)
                                            <span class="text-xs text-gray-400">({{ $user->classroom->major->name }})</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="editUser({{ $user->id }})" title="Edit / Reset Password"
                                            class="p-1.5 text-gray-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg transition">
                                            <x-icon name="pencil" class="w-4 h-4" />
                                        </button>
                                        @if($user->role !== 'admin')
                                            <button wire:click="deleteUser({{ $user->id }})"
                                                wire:confirm="Hapus akun {{ $user->name }}? Seluruh nilai ujiannya ikut terhapus."
                                                class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Hapus">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-gray-400 dark:text-gray-500 italic">Tidak ada pengguna yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal buat/edit akun --}}
        @if($isUserModalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 backdrop-blur-sm overflow-y-auto">
                <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 my-8">
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $editingUserId ? 'Edit Akun' : 'Buat Akun Baru' }}
                        </h3>
                        <button wire:click="closeUserModal" class="p-1.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveUser" class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                            <input type="text" wire:model="u_name"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('u_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email (untuk login)</label>
                            <input type="email" wire:model="u_email"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('u_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        @if(!$editingUserId || ($editingUserId && $u_role !== 'admin'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Role</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center gap-2 p-2.5 border rounded-lg cursor-pointer text-sm font-bold {{ $u_role === 'siswa' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400' }}">
                                        <input type="radio" wire:model.live="u_role" value="siswa" class="text-indigo-600 focus:ring-indigo-500">
                                        {{ term('siswa') }}
                                    </label>
                                    <label class="flex items-center gap-2 p-2.5 border rounded-lg cursor-pointer text-sm font-bold {{ $u_role === 'guru' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400' }}">
                                        <input type="radio" wire:model.live="u_role" value="guru" class="text-emerald-600 focus:ring-emerald-500">
                                        {{ term('guru') }}
                                    </label>
                                </div>
                                @error('u_role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        @if($u_role === 'siswa')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ term('kelas') }}</label>
                                <select wire:model="u_classroom_id"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tanpa {{ term('kelas') }}</option>
                                    @foreach($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}">{{ $classroom->name }}{{ $classroom->major ? ' - '.$classroom->major->name : '' }}</option>
                                    @endforeach
                                </select>
                                @error('u_classroom_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Password {{ $editingUserId ? '(kosongkan jika tidak diganti)' : '' }}
                            </label>
                            <input type="text" wire:model="u_password" autocomplete="new-password"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm font-mono focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Minimal 8 karakter">
                            @error('u_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" wire:click="closeUserModal"
                                class="w-1/3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold py-2.5 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm">Batal</button>
                            <button type="submit"
                                class="w-2/3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-lg transition text-sm shadow-sm">
                                {{ $editingUserId ? 'Simpan Perubahan' : 'Buat Akun' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif

    {{-- ================= TAB: KELAS & JURUSAN ================= --}}
    @if($tab === 'kelas')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Jurusan --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden h-fit">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-3">Jurusan{{ app_mode() === 'kampus' ? ' / Program Studi' : '' }}</h3>
                    <form wire:submit.prevent="createMajor" class="flex gap-2">
                        <input type="text" wire:model="m_name"
                            class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="{{ app_mode() === 'kampus' ? 'Contoh: Teknik Informatika' : 'Contoh: IPA' }}">
                        <button type="submit" class="px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm transition">Tambah</button>
                    </form>
                    @error('m_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($majors as $major)
                        <div class="px-5 py-3 flex items-center gap-3">
                            <div class="flex-1">
                                <p class="font-bold text-sm text-gray-800 dark:text-gray-100">{{ $major->name }}</p>
                                <p class="text-xs text-gray-400">{{ $major->classrooms_count }} {{ strtolower(term('kelas')) }}</p>
                            </div>
                            <button wire:click="deleteMajor({{ $major->id }})"
                                wire:confirm="Hapus jurusan {{ $major->name }}?"
                                class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 dark:text-gray-500 text-sm">Belum ada jurusan.</div>
                    @endforelse
                </div>
            </div>

            {{-- Kelas --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden h-fit">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-3">{{ term('kelas') }}</h3>
                    <form wire:submit.prevent="createClassroom" class="flex flex-col sm:flex-row gap-2">
                        <input type="text" wire:model="c_name"
                            class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="{{ app_mode() === 'kampus' ? 'Contoh: TI-3A' : 'Contoh: XII IPA 1' }}">
                        <select wire:model="c_major_id"
                            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tanpa Jurusan</option>
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}">{{ $major->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm transition">Tambah</button>
                    </form>
                    @error('c_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($classrooms as $classroom)
                        <div class="px-5 py-3">
                            @if($editingClassroomId === $classroom->id)
                                <form wire:submit.prevent="saveClassroom" class="flex flex-col sm:flex-row gap-2">
                                    <input type="text" wire:model="editingClassroomName"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                    <select wire:model="editingClassroomMajorId"
                                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                        <option value="">Tanpa Jurusan</option>
                                        @foreach($majors as $major)
                                            <option value="{{ $major->id }}">{{ $major->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex gap-2">
                                        <button type="submit" class="px-3 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg">Simpan</button>
                                        <button type="button" wire:click="cancelEditClassroom" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-lg">Batal</button>
                                    </div>
                                </form>
                                @error('editingClassroomName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @else
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <p class="font-bold text-sm text-gray-800 dark:text-gray-100">
                                            {{ $classroom->name }}
                                            @if($classroom->major)
                                                <span class="ml-1 text-[10px] font-bold uppercase bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded">{{ $classroom->major->name }}</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $classroom->students_count }} {{ strtolower(term('siswa')) }}</p>
                                    </div>
                                    <button wire:click="startEditClassroom({{ $classroom->id }})"
                                        class="p-1.5 text-gray-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg transition">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="deleteClassroom({{ $classroom->id }})"
                                        wire:confirm="Hapus {{ strtolower(term('kelas')) }} {{ $classroom->name }}? Anggotanya menjadi tanpa {{ strtolower(term('kelas')) }}."
                                        class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 dark:text-gray-500 text-sm">Belum ada {{ strtolower(term('kelas')) }}.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- ================= TAB: PENGATURAN APLIKASI ================= --}}
    @if($tab === 'pengaturan')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 h-fit">
                <h3 class="font-bold text-gray-800 dark:text-white mb-5">Identitas Aplikasi</h3>
                <form wire:submit.prevent="saveSettings" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Aplikasi Ujian</label>
                        <input type="text" wire:model="s_app_name"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="text-xs text-gray-400 mt-1">Tampil di navbar, halaman depan, dan judul tab browser.</p>
                        @error('s_app_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama {{ term('sekolah') }} / Instansi</label>
                        <input type="text" wire:model="s_institution_name"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Contoh: SMA Negeri 1 Medan">
                        @error('s_institution_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tahun Ajaran / Semester</label>
                        <input type="text" wire:model="s_academic_year"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Contoh: 2026/2027 Ganjil">
                        @error('s_academic_year') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Logo / Icon</label>
                            @if(cbt_logo_url())
                                <div class="mb-2 flex items-center gap-2">
                                    <img src="{{ cbt_logo_url() }}" class="h-12 w-12 object-contain rounded-lg border border-gray-200 dark:border-gray-600 bg-white">
                                    <button type="button" wire:click="removeLogo" class="text-xs text-red-500 font-bold hover:underline">Hapus</button>
                                </div>
                            @endif
                            <input type="file" wire:model="s_logo" accept="image/*"
                                class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                            <div wire:loading wire:target="s_logo" class="text-xs text-indigo-500 mt-1">Mengupload...</div>
                            @error('s_logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Banner Halaman Depan</label>
                            @if(cbt_banner_url())
                                <div class="mb-2 flex items-center gap-2">
                                    <img src="{{ cbt_banner_url() }}" class="h-12 w-24 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                                    <button type="button" wire:click="removeBanner" class="text-xs text-red-500 font-bold hover:underline">Hapus</button>
                                </div>
                            @endif
                            <input type="file" wire:model="s_banner" accept="image/*"
                                class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                            <div wire:loading wire:target="s_banner" class="text-xs text-indigo-500 mt-1">Mengupload...</div>
                            @error('s_banner') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="s_logo,s_banner"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white font-bold py-2.5 rounded-lg text-sm transition shadow-sm">
                        Simpan Pengaturan
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 h-fit">
                <h3 class="font-bold text-gray-800 dark:text-white mb-1">Mode Aplikasi</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Menentukan istilah di seluruh aplikasi ({{ term('siswa') }}, {{ term('guru') }}, dst).</p>
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="setMode('sekolah')"
                        class="py-3 rounded-xl border-2 font-bold text-sm transition {{ $currentMode === 'sekolah' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300' }}">
                        <x-icon name="building-library" stroke="1.8" class="w-5 h-5 mx-auto mb-1" />
                        Sekolah
                        <span class="block text-[10px] font-medium mt-0.5">Siswa &amp; Guru</span>
                    </button>
                    <button wire:click="setMode('kampus')"
                        class="py-3 rounded-xl border-2 font-bold text-sm transition {{ $currentMode === 'kampus' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300' }}">
                        <x-icon name="academic-cap" stroke="1.8" class="w-5 h-5 mx-auto mb-1" />
                        Kampus
                        <span class="block text-[10px] font-medium mt-0.5">Mahasiswa &amp; Dosen</span>
                    </button>
                </div>

                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    <p class="font-bold text-gray-600 dark:text-gray-300 mb-1.5">Catatan alur akun:</p>
                    Pendaftaran mandiri sudah <b>ditutup</b>. Seluruh akun {{ strtolower(term('siswa')) }} dan {{ strtolower(term('guru')) }}
                    dibuat oleh administrator lewat tab <b>Manajemen Pengguna</b>, lalu bagikan email + password ke pemilik akun.
                </div>
            </div>
        </div>
    @endif
</div>
