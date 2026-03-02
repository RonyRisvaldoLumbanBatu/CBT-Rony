<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 transition-colors duration-300">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-indigo-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                Penilaian Essay: {{ $exam->title }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-9">Kelola dan berikan skor untuk jawaban uraian siswa di sini.</p>
        </div>
        <a href="/guru/ujian" class="flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
            &laquo; Kembali
        </a>
    </div>

    @if(session('sukses'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-4 shadow-sm rounded-r-lg flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600 dark:text-green-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-green-700 dark:text-green-400 font-medium">{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">
                        <th class="py-4 px-6 font-bold border-b dark:border-gray-700">Mahasiswa</th>
                        <th class="py-4 px-6 font-bold border-b dark:border-gray-700 text-center">NIM / Email</th>
                        <th class="py-4 px-6 font-bold border-b dark:border-gray-700 text-center">Skor Akhir / Status</th>
                        <th class="py-4 px-6 font-bold border-b dark:border-gray-700 text-center">Aksi Penilaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                    @forelse($results as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="py-4 px-6 font-bold">{{ $r->user->name }}</td>
                            <td class="py-4 px-6 text-center text-sm">{{ $r->user->email }}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="text-2xl font-black {{ $r->score >= 60 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                    {{ $r->score }}
                                </span>
                                @if($hasEssay && !$r->is_essay_graded)
                                    <div class="mt-1">
                                        <span class="inline-block px-2 py-0.5 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 text-[10px] font-bold rounded-full uppercase">Peringatan: Essay Belum Dinilai</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($hasEssay)
                                    <button wire:click="openGradeModal({{ $r->id }})" class="flex items-center gap-1.5 px-3 py-1.5 {{ $r->is_essay_graded ? 'bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/40 dark:text-blue-400 dark:hover:bg-blue-900/60' : 'bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600' }} rounded-lg text-sm font-bold transition mx-auto cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                        </svg>
                                        {{ $r->is_essay_graded ? 'Ubah Nilai Essay' : 'Nilai Essay' }}
                                    </button>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400 italic">Tidak ada Soal Essay pada ujian ini.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-500 dark:text-gray-400 italic">Belum ada mahasiswa yang mengumpulkan ujian ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Penilaian Manual -->
    @if($isModalOpen && $selectedResult)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-gray-900/80 backdrop-blur-sm p-4 sm:p-6 p-4 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 w-full max-w-4xl max-h-[90vh] rounded-2xl shadow-xl flex flex-col relative border border-gray-100 dark:border-gray-700">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700 sticky top-0 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-t-2xl z-10">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-800 dark:text-gray-100">Penilaian Essay Mahasiswa</h3>
                        <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $selectedResult->user->name }} - {{ $selectedResult->user->email }}</p>
                    </div>
                    <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition" title="Tutup">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto shrink" style="max-height: calc(90vh - 160px);">
                    <form wire:submit.prevent="saveScores" class="space-y-8">
                        @forelse($essayQuestions as $index => $q)
                            <div class="bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                                <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                    <div class="flex items-start justify-between gap-4">
                                        <h4 class="font-bold text-gray-800 dark:text-gray-200 text-lg leading-relaxed flex-1">
                                            <span class="text-indigo-600 dark:text-indigo-400 mr-1">{{ $index + 1 }}.</span> {{ $q->question_text }}
                                        </h4>
                                        <div class="w-32 shrink-0">
                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 text-right">Skor (0-100)</label>
                                            <input type="number" wire:model="essayScores.{{ $q->id }}" min="0" max="100" class="w-full font-black text-center text-xl text-indigo-600 dark:text-indigo-400 border-gray-300 dark:border-gray-600 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition shadow-inner p-2">
                                            @error('essayScores.'.$q->id) <span class="text-red-500 dark:text-red-400 text-xs block mt-1 text-right">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="p-5 bg-transparent">
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Jawaban Mahasiswa:</label>
                                    <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-300 font-medium whitespace-pre-wrap leading-relaxed shadow-sm">
                                        {{ $essayAnswers[$q->id] }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-500 dark:text-gray-400 font-medium italic">Tidak ada jawaban tipe essay untuk mahasiswa ini.</div>
                        @endforelse

                        <!-- Modal Footer / Submit Float -->
                        <div class="sticky bottom-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md pt-4 pb-2 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3 mt-4">
                            <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-sm">Batal</button>
                            <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 dark:bg-indigo-500 rounded-xl hover:bg-indigo-700 dark:hover:bg-indigo-600 transition shadow-sm flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Simpan Penilaian Essay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
