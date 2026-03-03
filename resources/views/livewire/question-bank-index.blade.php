<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    {{-- Header Section --}}
    <div
        class="mb-8 flex justify-between items-center bg-white/20 backdrop-blur-md rounded-2xl p-6 border border-white/30 shadow-[0_8px_30px_rgb(0,0,0,0.12)]">
        <div>
            <h1
                class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600">
                Bank Soal
            </h1>
            <p class="mt-2 text-sm text-gray-500">Kelola kumpulan soal Anda yang dapat digunakan ulang untuk berbagai
                ujian.</p>
        </div>
        <div>
            <button wire:click="$set('isModalOpen', true)"
                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-700 hover:to-indigo-700 active:scale-95 transition-all duration-200 shadow-lg shadow-blue-500/30">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Kategori Baru
            </button>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Daftar Bank Soal --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($banks as $bank)
            <div
                class="bg-white/40 backdrop-blur-xl overflow-hidden shadow-xl rounded-2xl border border-white/50 relative group transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800 mb-2 truncate" title="{{ $bank->title }}">
                                {{ $bank->title }}
                            </h3>
                            <p class="text-sm text-gray-500 line-clamp-2">
                                {{ $bank->description ?? 'Tidak ada deskripsi.' }}
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <!-- Tombol Delete -->
                            <button wire:click="deleteBank({{ $bank->id }})"
                                wire:confirm="Yakin ingin menghapus Bank Soal ini beserta SEMUA soal di dalamnya? Tindakan ini tidak dapat diurungkan!"
                                class="p-2 text-red-500 hover:bg-red-100 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200/50 flex items-center justify-between">
                        <div class="text-sm font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                            {{ $bank->questions_count }} Soal Tersimpan
                        </div>

                        <a href="{{ route('guru.bank.show', $bank->id) }}" wire:navigate
                            class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                            Atur Soal
                            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="col-span-full bg-white/40 backdrop-blur-xl rounded-2xl border border-white/50 p-12 text-center text-gray-500">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
                <p class="text-lg font-medium text-gray-900">Belum Ada Bank Soal</p>
                <p class="mt-1">Buat Kategori Kumpulan Soal pertamamu sekarang.</p>
            </div>
        @endforelse
    </div>

    {{-- Modal Tambah Bank Soal --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Background backdrop -->
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"
                wire:click="$set('isModalOpen', false)"></div>

            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <!-- Modal panel -->
                <div
                    class="relative bg-white/90 backdrop-blur-2xl rounded-3xl text-left overflow-hidden shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] transform transition-all sm:my-8 sm:max-w-xl w-full border border-white/50">
                    <div class="px-8 pt-8 pb-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-2xl leading-6 font-bold text-gray-900 bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-indigo-600 mb-6"
                                    id="modal-title">
                                    Buat Bank Soal Baru
                                </h3>
                                <div class="mt-2 space-y-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Judul / Kategori <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" wire:model.defer="title"
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all bg-white/50"
                                            placeholder="Contoh: Logika Algoritma Dasar">
                                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi & Tag
                                            (Opsional)</label>
                                        <textarea wire:model.defer="description" rows="3"
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all bg-white/50"
                                            placeholder="Soal-soal latihan khusus mahasiswa semester 1..."></textarea>
                                        @error('description') <span
                                        class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Actions -->
                    <div class="bg-gray-50/50 px-8 py-5 border-t border-gray-100 sm:flex sm:flex-row-reverse rounded-b-3xl">
                        <button type="button" wire:click="createBank"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-base font-semibold text-white shadow-lg shadow-blue-500/30 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-all transform active:scale-95">
                            Simpan & Buat
                        </button>
                        <button type="button" wire:click="$set('isModalOpen', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 px-6 py-2.5 bg-white text-base font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all transform active:scale-95">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>