<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 transition-colors duration-300">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-8 h-8 text-indigo-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z">
                    </path>
                </svg>
                Analisis Butir Soal: {{ $exam->title }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-10 text-sm">Validasi kualitas soal berdasarkan tingkat
                kesukaran dan jawaban dari <span
                    class="font-bold text-indigo-600 dark:text-indigo-400">{{ $totalParticipants }} Peserta
                    Ujian</span>.</p>
        </div>
        <a href="/guru/ujian"
            class="flex items-center gap-2 px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
            &laquo; Kembali
        </a>
    </div>

    @if($totalParticipants === 0)
        <!-- Empty State klo blm ada yg tes -->
        <div
            class="bg-indigo-50 dark:bg-indigo-900/40 rounded-2xl p-8 border border-indigo-200 dark:border-indigo-800 flex flex-col items-center justify-center min-h-[400px]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-16 h-16 text-indigo-400 dark:text-indigo-500 mb-4 animate-pulse">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
            </svg>
            <h3 class="text-xl font-bold text-indigo-900 dark:text-indigo-200 mb-2">Belum Ada Mahasiswa yang Menyelesaikan
                Ujian</h3>
            <p class="text-indigo-700 dark:text-indigo-400 text-center max-w-sm">
                Analisis tingkat kesukaran soal memerlukan minimal 1 Peserta yang telah mengumpulkan ujiannya terlebih
                dahulu! Cek kembali esok hari.
            </p>
        </div>
    @else
        <!-- Grid Analisis -->
        <div class="space-y-6">
            @foreach($analysisData as $index => $item)
                @php
                    $q = $item['question'];
                @endphp
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col md:flex-row hover:shadow-md transition duration-300 group hover:border-indigo-100 dark:hover:border-indigo-900/50">

                    <!-- Kiri: Konten Soal -->
                    <div
                        class="w-full md:w-3/5 p-6 border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700 flex flex-col justify-between group-hover:bg-indigo-50/20 dark:group-hover:bg-indigo-900/10 transition">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span
                                    class="bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-400 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">SOAL
                                    {{ $index + 1 }}</span>
                                <span
                                    class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold px-2 py-1 rounded">{{ $item['type_label'] }}</span>
                            </div>
                            <h4
                                class="text-lg font-medium text-gray-800 dark:text-gray-200 leading-relaxed max-h-[120px] overflow-y-auto pr-2 custom-scrollbar">
                                {{ $q->question_text }}</h4>
                        </div>
                    </div>

                    <!-- Kanan: Statistik Kesukaran -->
                    <div class="w-full md:w-2/5 flex">
                        <div
                            class="w-1/2 p-6 flex flex-col items-center justify-center border-r border-gray-100 dark:border-gray-700">
                            <h5
                                class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide text-center">
                                Tingkat Kesukaran</h5>
                            <div class="relative w-24 h-24 mb-3">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="48" cy="48" r="40" fill="transparent"
                                        stroke="{{ $item['tingkat_kesukaran'] > 70 ? '#10b981' : ($item['tingkat_kesukaran'] < 30 ? '#ef4444' : '#6366f1') }}20"
                                        stroke-width="8" />
                                    <circle cx="48" cy="48" r="40" fill="transparent"
                                        stroke="{{ $item['tingkat_kesukaran'] > 70 ? '#10b981' : ($item['tingkat_kesukaran'] < 30 ? '#ef4444' : '#6366f1') }}"
                                        stroke-width="8" stroke-dasharray="251"
                                        stroke-dashoffset="{{ 251 - (251 * $item['tingkat_kesukaran'] / 100) }}"
                                        class="transition-all duration-1000 ease-out" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center flex-col">
                                    <span
                                        class="text-xl font-black text-gray-800 dark:text-gray-100">{{ $item['tingkat_kesukaran'] }}<span
                                            class="text-sm font-semibold text-gray-500">%</span></span>
                                </div>
                            </div>
                            <span
                                class="px-3 py-1 text-xs font-bold rounded-lg uppercase tracking-wider {{ $item['warna_label'] }} whitespace-nowrap">{{ $item['kategori_kesulitan'] }}</span>
                        </div>

                        <!-- Kanan Paling Ujung: Rasio Benar Salah / Pie -->
                        <div class="w-1/2 p-6 flex flex-col justify-center">
                            <h5
                                class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wide text-left">
                                Rasio Menjawab:</h5>
                            <div class="space-y-3">
                                <div>
                                    <div class="flex justify-between items-center text-sm font-semibold mb-1">
                                        <span class="text-green-600 dark:text-green-400 flex items-center gap-1.5">
                                            <div class="w-2 h-2 rounded-full bg-green-500"></div> Benar
                                        </span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $item['benar'] }} org</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full"
                                            style="width: {{ $totalParticipants > 0 ? ($item['benar'] / $totalParticipants) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between items-center text-sm font-semibold mb-1">
                                        <span class="text-red-500 dark:text-red-400 flex items-center gap-1.5">
                                            <div class="w-2 h-2 rounded-full bg-red-500"></div> Salah
                                        </span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $item['salah'] }} org</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-red-500 h-2 rounded-full"
                                            style="width: {{ $totalParticipants > 0 ? ($item['salah'] / $totalParticipants) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>