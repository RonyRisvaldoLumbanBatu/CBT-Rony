<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 relative transition-colors duration-300">

    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}"
                class="p-2 bg-white dark:bg-gray-800 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white">Kelola Soal</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ujian: <span
                        class="font-bold text-indigo-600 dark:text-indigo-400">{{ $exam->title }}</span></p>
            </div>
        </div>

        <button wire:click="openModal"
            class="flex items-center gap-2 bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Manual
        </button>
    </div>

    @if(session('sukses'))
        <div
            class="mb-6 bg-green-50 dark:bg-green-900/40 border-l-4 border-green-500 dark:border-green-400 p-4 shadow-sm rounded-r-lg flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-6 h-6 text-green-600 dark:text-green-400">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="text-green-700 dark:text-green-300 font-medium">{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1 space-y-4">
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 h-fit sticky top-10 transition-colors duration-300">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 dark:border-gray-700 pb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-emerald-500 dark:text-emerald-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Import dari Excel</h3>
                </div>

                <button wire:click="downloadTemplate"
                    class="w-full flex justify-center items-center gap-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold py-2.5 px-4 rounded-xl shadow-sm transition mb-4 text-sm border border-gray-200 dark:border-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Download Template (.xlsx)
                </button>

                <form wire:submit.prevent="importExcel">
                    <div class="mb-4 flex flex-col items-center justify-center w-full">
                        <label for="dropzone-file"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-2 text-gray-400 dark:text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Pilih file Excel/CSV
                                </p>
                            </div>
                            <input wire:model="file" id="dropzone-file" type="file" class="hidden"
                                accept=".xlsx, .xls, .csv" />
                        </label>
                    </div>

                    @if ($file)
                        <div
                            class="mb-4 text-sm text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/30 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            File: {{ $file->getClientOriginalName() }}
                        </div>
                    @endif
                    @error('file') <span class="text-red-500 dark:text-red-400 text-xs mb-4 block">{{ $message }}</span>
                    @enderror

                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full flex justify-center items-center gap-2 bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 disabled:bg-gray-400 dark:disabled:bg-gray-600 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition">
                        <span wire:loading.remove wire:target="importExcel">Mulai Import</span>
                        <span wire:loading wire:target="importExcel" class="animate-pulse">Menyihir Data...</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Daftar Soal Saat Ini
                ({{ $questions->count() }})</h3>

            @forelse($questions as $index => $q)
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 relative group transition-colors duration-300">

                    <div
                        class="absolute top-4 right-4 flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition">
                        <button wire:click="editQuestion({{ $q->id }})"
                            class="text-gray-400 dark:text-gray-500 hover:text-amber-500 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/40 p-1.5 rounded-lg transition"
                            title="Edit Soal">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        <button wire:click="deleteQuestion({{ $q->id }})" wire:confirm="Hapus soal ini?"
                            class="text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/40 p-1.5 rounded-lg transition"
                            title="Hapus Soal">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>

                    <p class="font-bold text-gray-800 dark:text-gray-100 mb-4 pr-16 leading-relaxed">
                        <span class="text-indigo-600 dark:text-indigo-400 mr-2">{{ $index + 1 }}.</span>

                        @if($q->type === 'essay')
                            <span class="inline-block px-2 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 text-xs font-bold rounded mr-2 uppercase tracking-wide">Essay</span>
                        @elseif($q->type === 'pg_kompleks')
                            <span class="inline-block px-2 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 text-xs font-bold rounded mr-2 uppercase tracking-wide">PG Kompleks</span>
                        @elseif($q->type === 'benar_salah')
                            <span class="inline-block px-2 py-1 bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 text-xs font-bold rounded mr-2 uppercase tracking-wide">Benar/Salah</span>
                        @elseif($q->type === 'isian')
                            <span class="inline-block px-2 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-bold rounded mr-2 uppercase tracking-wide">Isian</span>
                        @else
                            <span class="inline-block px-2 py-1 bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400 text-xs font-bold rounded mr-2 uppercase tracking-wide">PG</span>
                        @endif

                        {{ $q->question_text }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($q->options as $opt)
                            <div
                                class="px-4 py-2 rounded-lg border text-sm font-medium {{ $opt->is_correct ? 'bg-green-50 border-green-200 text-green-700 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300' : 'bg-gray-50 border-gray-200 text-gray-600 dark:bg-gray-700/50 dark:border-gray-600 dark:text-gray-400' }}">
                                {{ $opt->option_text }}
                                @if($opt->is_correct)
                                    <span
                                        class="float-right bg-green-200 dark:bg-green-800/80 text-green-800 dark:text-green-200 text-[10px] px-2 py-0.5 rounded-full uppercase font-bold">Benar</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div
                    class="p-10 text-center flex flex-col items-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-800/50">
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Ujian ini belum memiliki soal.</p>
                </div>
            @endforelse
        </div>

    </div>

    @if($isModalOpen)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900 bg-opacity-50 dark:bg-opacity-80 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-2xl p-4">
                <div
                    class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">

                    <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                            {{ $isEditMode ? 'Edit Soal' : 'Tambah Soal Manual' }}
                        </h3>
                        <button wire:click="closeModal"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition">
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveQuestion" class="p-6 space-y-5">

                        <div x-data="{ type: @entangle('type') }">
                            <div class="mb-5 relative">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tipe
                                    Soal</label>
                                <div x-data="{ 
                                            isOpen: false, 
                                            options: [
                                                { value: 'pg', label: 'Pilihan Ganda (Satu Jawaban Benar)' },
                                                { value: 'pg_kompleks', label: 'Pilihan Ganda Kompleks (Banyak Jawaban Benar)' },
                                                { value: 'benar_salah', label: 'Benar / Salah (True / False)' },
                                                { value: 'isian', label: 'Isian Singkat (Penilaian Otomatis)' },
                                                { value: 'essay', label: 'Uraian / Essay (Penilaian Manual Guru)' }
                                            ],
                                            get selectedLabel() { return this.options.find(o => o.value === type)?.label || 'Pilih Tipe Soal' }
                                        }" class="relative">

                                    <!-- Select Asli Disembunyikan, tetap sinkron dengan Livewire -->
                                    <select wire:model.live="type" class="hidden">
                                        <option value="pg">pg</option>
                                        <option value="pg_kompleks">pg_kompleks</option>
                                        <option value="benar_salah">benar_salah</option>
                                        <option value="isian">isian</option>
                                        <option value="essay">essay</option>
                                    </select>

                                    <!-- Tombol Pemicu -->
                                    <button type="button" @click="isOpen = !isOpen" @click.away="isOpen = false"
                                        class="w-full flex items-center justify-between rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/20 dark:focus:ring-indigo-400/20 hover:border-indigo-400 dark:hover:border-indigo-500 transition-all duration-300 font-medium py-3 px-4 cursor-pointer">

                                        <span x-text="selectedLabel" class="block truncate"></span>

                                        <span
                                            class="pointer-events-none flex items-center shrink-0 text-gray-500 dark:text-gray-400">
                                            <svg class="h-5 w-5 transition-transform duration-300"
                                                :class="isOpen ? 'rotate-180 text-indigo-500' : ''"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>

                                    <!-- Opsi Dropdown Melayang -->
                                    <ul x-show="isOpen" style="display: none;"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                        class="absolute z-50 mt-2 w-full max-h-60 overflow-auto rounded-xl bg-white dark:bg-gray-800 py-2 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none dark:ring-gray-700">

                                        <template x-for="option in options" :key="option.value">
                                            <li @click="type = option.value; isOpen = false;"
                                                class="relative cursor-pointer select-none py-3 pl-4 pr-9 transition-colors text-gray-900 dark:text-gray-100 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center justify-between"
                                                :class="type === option.value ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 font-bold' : 'font-medium'">

                                                <span x-text="option.label" class="block truncate"></span>

                                                <span x-show="type === option.value"
                                                    class="text-indigo-600 dark:text-indigo-400 absolute inset-y-0 right-0 flex items-center pr-4">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </li>
                                        </template>

                                    </ul>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label
                                    class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pertanyaan</label>
                                <textarea wire:model="question_text" rows="3"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition"
                                    placeholder="Ketik pertanyaan di sini..."></textarea>
                                @error('question_text') <span
                                class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Opsi Khusus Pilihan Ganda -->
                            <div x-show="type === 'pg' || type === 'pg_kompleks'" x-cloak>
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900/40 p-4 rounded-xl border border-gray-100 dark:border-gray-700 mb-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Opsi
                                            A</label>
                                        <input type="text" wire:model="opsi_a"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm">
                                        @error('opsi_a') <span
                                            class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Opsi
                                            B</label>
                                        <input type="text" wire:model="opsi_b"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm">
                                        @error('opsi_b') <span
                                            class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Opsi
                                            C</label>
                                        <input type="text" wire:model="opsi_c"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm">
                                        @error('opsi_c') <span
                                            class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Opsi
                                            D</label>
                                        <input type="text" wire:model="opsi_d"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm">
                                        @error('opsi_d') <span
                                            class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div x-show="type === 'pg'">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kunci
                                        Jawaban
                                        Benar</label>
                                    <div class="flex gap-4">
                                        @foreach(['A', 'B', 'C', 'D'] as $huruf)
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" wire:model="jawaban_benar" value="{{ $huruf }}"
                                                    class="w-4 h-4 text-indigo-600 dark:text-indigo-500 dark:bg-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                                                <span class="text-gray-700 dark:text-gray-300 font-bold">{{ $huruf }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('jawaban_benar') <span
                                    class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div x-show="type === 'pg_kompleks'" x-cloak>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kunci
                                        Jawaban Benar (Bisa lebih dari 1)</label>
                                    <div class="flex gap-4">
                                        @foreach(['A', 'B', 'C', 'D'] as $huruf)
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" wire:model="jawaban_benar_kompleks" value="{{ $huruf }}"
                                                    class="w-4 h-4 rounded text-indigo-600 dark:text-indigo-500 dark:bg-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                                                <span class="text-gray-700 dark:text-gray-300 font-bold">{{ $huruf }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('jawaban_benar_kompleks') <span
                                    class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Opsi Khusus Benar Salah -->
                            <div x-show="type === 'benar_salah'" x-cloak
                                class="mb-5 bg-gray-50 dark:bg-gray-900/40 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pilih Kunci
                                    Jawaban</label>
                                <div class="flex gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="jawaban_benar_bs" value="Benar"
                                            class="w-5 h-5 text-indigo-600 dark:text-indigo-500 dark:bg-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                                        <span class="text-gray-700 dark:text-gray-300 font-bold">Benar</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="jawaban_benar_bs" value="Salah"
                                            class="w-5 h-5 text-indigo-600 dark:text-indigo-500 dark:bg-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                                        <span class="text-gray-700 dark:text-gray-300 font-bold">Salah</span>
                                    </label>
                                </div>
                                @error('jawaban_benar_bs') <span
                                class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Opsi Khusus Isian -->
                            <div x-show="type === 'isian'" x-cloak
                                class="mb-5 bg-gray-50 dark:bg-gray-900/40 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kunci Jawaban
                                    Singkat</label>
                                <input type="text" wire:model="kunci_isian"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition"
                                    placeholder="Tulis satu jawaban atau angka mutlak contoh: Jakarta">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Sistem akan menilai benar jika
                                    huruf yang diketik siswa sama persis. (Huruf kapital/kecil diabaikan saat menilai jika
                                    diinginkan, namun sebaiknya patokan harus jelas).</p>
                                @error('kunci_isian') <span
                                class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" wire:click="closeModal"
                                class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition">Batal</button>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 dark:bg-indigo-500 rounded-xl hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">
                                {{ $isEditMode ? 'Simpan Perubahan' : 'Tambahkan Soal' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif
</div>