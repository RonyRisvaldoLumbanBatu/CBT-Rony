<div class="max-w-5xl mx-auto py-6 sm:py-10 px-4 sm:px-6 lg:px-8">

    <div class="mb-6 sm:mb-8">
        <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Kelola {{ term('kelas') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Atur data {{ strtolower(term('kelas')) }} dan mode aplikasi di sini.</p>
    </div>

    @if(session('sukses'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-4 rounded-r-xl flex items-center gap-3">
            <x-icon name="check-circle" stroke="1.5" class="w-6 h-6 shrink-0 text-green-600 dark:text-green-400" />
            <span class="text-green-700 dark:text-green-400 font-medium text-sm">{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom kiri: form + mode --}}
        <div class="space-y-6">
            {{-- Tambah kelas --}}
            <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-icon name="plus" class="w-5 h-5 text-indigo-500" />
                    Tambah {{ term('kelas') }}
                </h3>
                <form wire:submit.prevent="createClassroom" class="space-y-3">
                    <input type="text" wire:model="name"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ app_mode() === 'kampus' ? 'Contoh: TI-3A' : 'Contoh: XII IPA 1' }}">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl text-sm transition">
                        Simpan
                    </button>
                </form>
            </div>

            {{-- Mode aplikasi --}}
            <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">Mode Aplikasi</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Menentukan istilah di seluruh aplikasi ({{ term('siswa') }}, {{ term('guru') }}, dst).</p>
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="setMode('sekolah')"
                        class="py-3 rounded-xl border-2 font-bold text-sm transition {{ $currentMode === 'sekolah' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300' }}">
                        🏫 Sekolah
                        <span class="block text-[10px] font-medium mt-0.5">Siswa &amp; Guru</span>
                    </button>
                    <button wire:click="setMode('kampus')"
                        class="py-3 rounded-xl border-2 font-bold text-sm transition {{ $currentMode === 'kampus' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300' }}">
                        🎓 Kampus
                        <span class="block text-[10px] font-medium mt-0.5">Mahasiswa &amp; Dosen</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: daftar kelas --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 dark:text-white">Daftar {{ term('kelas') }}</h3>
                    <span class="text-xs font-bold text-gray-400 bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-full">{{ $classrooms->count() }} {{ strtolower(term('kelas')) }}</span>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($classrooms as $classroom)
                        <div class="p-4 sm:px-6 flex items-center gap-3">
                            @if($editingId === $classroom->id)
                                <form wire:submit.prevent="saveEdit" class="flex-1 flex flex-col sm:flex-row gap-2">
                                    <input type="text" wire:model="editingName"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">
                                    <div class="flex gap-2">
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700">Simpan</button>
                                        <button type="button" wire:click="cancelEdit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-lg">Batal</button>
                                    </div>
                                </form>
                                @error('editingName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @else
                                <div class="w-10 h-10 shrink-0 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-sm">
                                    {{ strtoupper(mb_substr($classroom->name, 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-900 dark:text-white truncate">{{ $classroom->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $classroom->students_count }} {{ strtolower(term('siswa')) }}</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button wire:click="startEdit({{ $classroom->id }})"
                                        class="p-2 text-gray-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg transition" title="Ubah nama">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="deleteClassroom({{ $classroom->id }})"
                                        wire:confirm="Hapus {{ strtolower(term('kelas')) }} ini? Anggotanya akan menjadi tanpa {{ strtolower(term('kelas')) }}."
                                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Hapus">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">Belum ada {{ strtolower(term('kelas')) }}.</p>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Buat {{ strtolower(term('kelas')) }} pertama lewat form di samping. {{ term('siswa') }} baru akan memilih {{ strtolower(term('kelas')) }} saat mendaftar.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
