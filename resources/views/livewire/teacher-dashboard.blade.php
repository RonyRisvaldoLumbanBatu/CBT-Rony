<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 transition-colors duration-300">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-4 hover:shadow-md transition">
            <div class="p-4 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Ujian Dibuat</p>
                <p class="text-3xl font-extrabold text-gray-800 dark:text-gray-100">{{ $totalUjian }}</p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-4 hover:shadow-md transition">
            <div class="p-4 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Ujian Berstatus Aktif</p>
                <p class="text-3xl font-extrabold text-gray-800 dark:text-gray-100">{{ $ujianAktif }}</p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-4 hover:shadow-md transition">
            <div class="p-4 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Bank Soal</p>
                <p class="text-3xl font-extrabold text-gray-800 dark:text-gray-100">{{ $totalSoal }}</p>
            </div>
        </div>
    </div>

    @if(session('sukses'))
        <div
            class="mb-6 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-4 shadow-sm rounded-r-lg flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-6 h-6 text-green-600 dark:text-green-400">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="text-green-700 dark:text-green-400 font-medium">{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 h-fit sticky top-10 {{ $isEditMode ? 'ring-2 ring-amber-400 dark:ring-amber-500' : '' }}">
            <div class="flex items-center gap-2 mb-6 border-b dark:border-gray-700 pb-4">
                @if($isEditMode)
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-amber-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-indigo-600 dark:text-indigo-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                @endif
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                    {{ $isEditMode ? 'Edit Data Ujian' : 'Buat Ujian Baru' }}
                </h3>
            </div>

            <form wire:submit.prevent="{{ $isEditMode ? 'updateExam' : 'createExam' }}" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Judul Ujian</label>
                    <input type="text" wire:model="title"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                        placeholder="Contoh: Ujian Akhir Semester Genap">
                    @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi
                        (Opsional)</label>
                    <textarea wire:model="description"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                        rows="3" placeholder="Deskripsi singkat..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Durasi Waktu
                        (Menit)</label>
                    <input type="number" wire:model="time_limit"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                    @error('time_limit') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    @if($isEditMode)
                        <button type="button" wire:click="cancelEdit"
                            class="w-1/3 flex justify-center items-center bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold py-2.5 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Batal
                        </button>
                    @endif
                    <button type="submit"
                        class="{{ $isEditMode ? 'w-2/3 bg-amber-500 hover:bg-amber-600' : 'w-full bg-indigo-600 hover:bg-indigo-700' }} flex justify-center items-center gap-2 text-white font-bold py-2.5 px-4 rounded-lg transition shadow-sm">
                        {{ $isEditMode ? 'Update Data' : 'Simpan Ujian' }}
                    </button>
                </div>
            </form>
        </div>

        <div
            class="md:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">

            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-indigo-600 dark:text-indigo-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                    </svg>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Daftar Ujian Tersedia</h3>
                </div>

                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm transition"
                        placeholder="Cari judul ujian...">
                </div>
            </div>

            <div class="space-y-4">
                @forelse($exams as $exam)
                    <div
                        class="group border border-gray-200 dark:border-gray-700 rounded-xl p-5 bg-white dark:bg-gray-800 hover:bg-indigo-50/30 dark:hover:bg-indigo-900/20 hover:border-indigo-200 dark:hover:border-indigo-800 transition duration-300 shadow-sm flex flex-col relative">

                        <div
                            class="absolute top-4 right-4 flex items-center gap-1 opacity-50 group-hover:opacity-100 transition">
                            <button wire:click="editExam({{ $exam->id }})"
                                class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/40 rounded-lg transition"
                                title="Edit Ujian">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>
                            <button wire:click="confirmDelete({{ $exam->id }})"
                                class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/40 rounded-lg transition"
                                title="Hapus Ujian">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4 pr-16">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 mb-2">
                                <h4
                                    class="text-xl font-bold text-gray-800 dark:text-gray-100 group-hover:text-indigo-700 dark:group-hover:text-indigo-400 transition">
                                    {{ $exam->title }}
                                </h4>
                                @if($exam->is_active)
                                    <span
                                        class="bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-500 px-2.5 py-1 rounded-md text-xs font-bold border border-green-200 dark:border-green-800 tracking-wide">AKTIF</span>
                                    <span
                                        class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 px-3 py-1 rounded-md text-xs font-bold border border-indigo-200 dark:border-indigo-800 tracking-wider flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                        </svg>
                                        PIN: {{ $exam->token }}
                                    </span>
                                @else
                                    <span
                                        class="bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 px-2.5 py-1 rounded-md text-xs font-bold border border-gray-200 dark:border-gray-600 tracking-wide">DRAFT</span>
                                @endif
                            </div>

                            <div class="text-sm text-gray-500 dark:text-gray-400 flex flex-wrap gap-3 mt-3">
                                <span
                                    class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 px-3 py-1.5 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-4 h-4 text-gray-400 dark:text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    {{ $exam->time_limit }} Menit
                                </span>
                                <span
                                    class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 px-3 py-1.5 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-4 h-4 text-gray-400 dark:text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    {{ $exam->questions->count() }} Soal
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 border-t border-gray-100 dark:border-gray-700 pt-4 mt-auto">
                            <button wire:click="toggleStatus({{ $exam->id }})"
                                class="flex items-center gap-2 bg-white dark:bg-gray-800 border {{ $exam->is_active ? 'border-amber-200 dark:border-amber-700 text-amber-600 dark:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/30' : 'border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30' }} px-4 py-2 rounded-lg text-sm font-semibold transition">
                                @if($exam->is_active)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                    Tutup Ujian
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    Buka Ujian
                                @endif
                            </button>

                            <a href="/guru/ujian/{{ $exam->id }}/soal"
                                class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 text-emerald-600 dark:text-emerald-500 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                Kelola Soal
                            </a>

                            <a href="/guru/ujian/{{ $exam->id }}/export"
                                class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-rose-200 dark:border-rose-700 text-rose-600 dark:text-rose-500 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rose-50 dark:hover:bg-rose-900/30 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                Export PDF
                            </a>

                            <a href="/guru/ujian/{{ $exam->id }}/export-excel"
                                class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 text-green-600 dark:text-green-500 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-50 dark:hover:bg-green-900/30 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                Export Excel
                            </a>

                            <a href="/pengawas/{{ $exam->id }}" target="_blank"
                                class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-indigo-200 dark:border-indigo-700 text-indigo-600 dark:text-indigo-400 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.348 14.651a3.75 3.75 0 0 1 0-5.303m5.304 0a3.75 3.75 0 0 1 0 5.303m-7.425 2.122a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M3.75 20.25A12 75 12 75 0 0 1 3.75 3.75m16.5 0a12 75 12 75 0 0 1 0 16.5" />
                                </svg>
                                Radar
                            </a>
                        </div>
                    </div>
                @empty
                    <div
                        class="p-10 text-center flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada ujian yang ditemukan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($isDeleteModalOpen)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-md p-4">
                <div
                    class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                    <button wire:click="cancelDelete"
                        class="absolute top-4 right-4 text-gray-400 dark:text-gray-500 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                    <div class="p-6 text-center">
                        <div
                            class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-5">
                            <svg class="h-10 w-10 text-red-600 dark:text-red-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-extrabold text-gray-800 dark:text-gray-100">Yakin ingin menghapus?</h3>
                        <p class="mb-8 text-sm text-gray-500 dark:text-gray-400 font-medium leading-relaxed">Semua soal dan
                            nilai siswa di dalam ujian ini akan <b class="text-red-500 xl:text-red-400">ikut terhapus
                                selamanya</b>. Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="flex justify-center gap-3">
                            <button wire:click="cancelDelete"
                                class="px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition w-full">Batal</button>
                            <button wire:click="executeDelete"
                                class="px-5 py-2.5 text-sm font-bold text-white bg-red-600 dark:bg-red-500 rounded-xl hover:bg-red-700 dark:hover:bg-red-600 transition w-full">Ya,
                                Hapus Permanen</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>