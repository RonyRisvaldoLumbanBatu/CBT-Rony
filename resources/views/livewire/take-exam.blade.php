<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8 select-none" x-data="{ 
         warnings: 0, 
         maxWarnings: 3,
         showWarning: false,
         showSubmitConfirm: false,
         init() {
            // Anti Klik Kanan
            document.addEventListener('contextmenu', event => event.preventDefault());
            // Anti Copy Text
            document.addEventListener('copy', event => {
                event.preventDefault();
                alert('Tindakan menyalin tidak diizinkan!');
            });
            // Pelacak Tab Focus
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.warnings++;
                    this.showWarning = true;
                    if (this.warnings >= this.maxWarnings) {
                        $wire.submitExam(); // Auto submit pada pelanggaran ke-3
                    } else {
                        $wire.logCheatStrike(this.warnings);
                    }
                }
            });
         }
     }">

    <!-- Modal Peringatan Kecurangan (Anti-Cheat) -->
    <div x-show="showWarning" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/90 backdrop-blur-md">
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-lg w-full shadow-2xl border-4 border-red-500 text-center animate-bounce">
            <div
                class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-900/30 mb-6 focus:ring-4 ring-red-500">
                <svg class="h-12 w-12 text-red-600 dark:text-red-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3
                class="text-3xl font-black text-gray-900 dark:text-white mb-3 uppercase tracking-wider text-red-600 dark:text-red-500">
                Peringatan Keras!</h3>
            <p class="text-lg text-gray-600 dark:text-gray-300 font-medium mb-8 leading-relaxed">
                Sistem Radar mendeteksi Anda baru saja <span class="font-bold underline text-red-500">KELUAR DARI
                    HALAMAN UJIAN</span>! <br><br>
                Ini adalah pelanggaran <strong class="text-red-600 text-2xl ml-1" x-text='warnings'></strong> dari batas
                <strong x-text='maxWarnings' class="text-xl"></strong> maksimal. <br>
                Jika melebihi batas, ujian Anda akan <strong class="text-red-500">disubmit secara otomatis!</strong>
            </p>
            <button @click="showWarning = false"
                class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-black text-lg py-4 px-6 rounded-xl transition-all duration-300 shadow-xl shadow-red-500/30 uppercase tracking-widest">
                SAYA BERJANJI TIDAK MENGULANGI
            </button>
        </div>
    </div>

    <!-- Modal Konfirmasi Kumpul Ujian (Submit) -->
    <div x-show="showSubmitConfirm" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/90 backdrop-blur-sm transition-opacity">
        <div @click.away="showSubmitConfirm = false"
            class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-sm w-full shadow-2xl border border-gray-100 dark:border-gray-700 text-center transform transition-all duration-300 scale-100">
            <div
                class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-5 ring-4 ring-emerald-50 dark:ring-emerald-900/10">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Kumpulkan Ujian?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-6 leading-relaxed">
                Pastikan sumua jawabanmu sudah <strong>terisi dan benar</strong>. Setelah dikumpulkan, ujian akan
                langsung berakhir.
            </p>
            <div class="flex flex-col gap-3">
                <button wire:click="submitExam" @click="showSubmitConfirm = false"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base py-3.5 px-4 rounded-xl transition-all shadow-lg shadow-emerald-500/30 active:scale-95 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                    Ya, Kumpulkan Sekarang
                </button>
                <button @click="showSubmitConfirm = false"
                    class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold text-base py-3.5 px-4 rounded-xl transition-all active:scale-95">
                    Batal, Ingin Cek Lagi
                </button>
            </div>
        </div>
    </div>


    @if(!$isPinVerified)
        <div class="min-h-[70vh] flex flex-col items-center justify-center p-4">
            <div
                class="bg-white dark:bg-gray-800 p-8 md:p-10 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 w-full max-w-md text-center transition-all duration-300 transform scale-100">
                <div
                    class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mb-6 focus:ring-4 ring-indigo-500">
                    <svg class="h-10 w-10 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>

                <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">Ruang Ujian Terkunci</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">Masukkan 6-Digit PIN Akses yang
                    diberikan oleh dosen atau pengawas untuk memulai <strong>{{ $exam->title }}</strong>.</p>

                <form wire:submit.prevent="verifyPin" class="space-y-6">
                    <div>
                        <input type="text" wire:model="inputPin"
                            class="w-full text-center text-3xl font-black tracking-[0.2em] rounded-2xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-indigo-400 shadow-inner px-6 py-4 uppercase focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition placeholder:text-gray-300 dark:placeholder:text-gray-700 placeholder:font-medium placeholder:tracking-normal"
                            placeholder="PIN" maxlength="6" required autofocus autocomplete="off">

                        @if (session()->has('pin_error'))
                            <p class="text-red-500 text-sm font-bold mt-3 animate-pulse flex items-center justify-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ session('pin_error') }}
                            </p>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-4 px-4 border border-transparent rounded-2xl text-lg font-black text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-xl shadow-indigo-500/30 transition-all duration-300 cursor-pointer">
                        Validasi & Mulai Ujian
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>
                    </button>
                </form>

                <div class="mt-8 text-sm text-gray-400 dark:text-gray-500 font-medium">
                    Sistem Penilaian Cerdas CBT &copy; {{ date('Y') }}
                </div>
            </div>
        </div>
    @else
        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4 sticky top-0 z-10 transition-colors duration-300">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white tracking-tight">{{ $exam->title }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kerjakan dengan jujur dan teliti.</p>
            </div>

            <div class="flex items-center gap-3 bg-indigo-50 dark:bg-gray-700 border border-indigo-100 dark:border-gray-600 px-5 py-2.5 rounded-xl transition-colors duration-300"
                x-data="{ 
                                        timeLeft: @entangle('timeLeft'),
                                        init() {
                                            setInterval(() => {
                                                if (this.timeLeft > 0) {
                                                    this.timeLeft--;
                                                } else if (this.timeLeft === 0) {
                                                    $wire.submitExam(); // Kumpulkan skor otomatis saat waktu habis
                                                    this.timeLeft = -1; // Hindari pengulangan trigger
                                                }
                                            }, 1000);
                                        },
                                        formatTime(seconds) {
                                            if (seconds <= 0) return '0:00';
                                            let m = Math.floor(seconds / 60);
                                            let s = seconds % 60;
                                            return m + ':' + s.toString().padStart(2, '0');
                                        }
                                     }">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6 text-indigo-600 dark:text-indigo-400 animate-pulse">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <div :class="timeLeft < 300 ? 'text-red-600 dark:text-red-400' : 'text-indigo-700 dark:text-indigo-300'"
                    class="text-xl font-bold font-mono tracking-wider" x-text="formatTime(timeLeft)">
                    {{ floor($timeLeft / 60) }}:{{ str_pad($timeLeft % 60, 2, '0', STR_PAD_LEFT) }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <div
                class="lg:col-span-3 bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300">

                @php
                    $currentQuestion = $questionsData[$currentQuestionIndex];
                @endphp

                <div wire:key="soal-area-{{ $currentQuestion['id'] }}">
                    <div class="mb-8 border-b border-gray-100 dark:border-gray-700 pb-6 transition-colors duration-300">
                        <span
                            class="inline-block bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 text-xs font-bold px-3 py-1.5 rounded-full mb-4">
                            SOAL NOMOR {{ $currentQuestionIndex + 1 }}
                        </span>
                        <p class="text-xl text-gray-800 dark:text-gray-100 font-medium leading-relaxed">
                            {{ $currentQuestion['question_text'] }}
                        </p>
                    </div>

                    <div class="space-y-4 mb-10">
                        @if($currentQuestion['type'] === 'essay')
                            <textarea wire:model.live="answers.{{ $currentQuestion['id'] }}" rows="8"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white shadow-inner focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition p-4 font-medium"
                                placeholder="Ketik jawaban lengkap Anda di sini..."></textarea>
                        @elseif($currentQuestion['type'] === 'isian')
                            <input type="text" wire:model.live="answers.{{ $currentQuestion['id'] }}"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white shadow-inner focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition p-4 font-medium"
                                placeholder="Ketik jawaban singkat di sini...">
                        @elseif($currentQuestion['type'] === 'pg' || $currentQuestion['type'] === 'benar_salah')
                            @foreach($currentQuestion['options'] as $option)
                                <label wire:key="opsi-{{ $option['id'] }}"
                                    class="flex items-center space-x-4 p-4 border rounded-xl cursor-pointer transition-all duration-200 {{ isset($answers[$currentQuestion['id']]) && $answers[$currentQuestion['id']] == $option['id'] ? 'bg-indigo-50 dark:bg-indigo-900/40 border-indigo-500 dark:border-indigo-400 ring-1 ring-indigo-500 dark:ring-indigo-400' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:border-gray-300 dark:hover:border-gray-600' }}">

                                    <input type="radio" name="jawaban_soal_{{ $currentQuestion['id'] }}"
                                        wire:model.live="answers.{{ $currentQuestion['id'] }}" value="{{ $option['id'] }}"
                                        class="w-5 h-5 text-indigo-600 dark:text-indigo-400 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:bg-gray-800 dark:checked:bg-indigo-400">

                                    <span
                                        class="text-gray-700 dark:text-gray-200 font-medium text-lg {{ isset($answers[$currentQuestion['id']]) && $answers[$currentQuestion['id']] == $option['id'] ? 'dark:text-indigo-100' : '' }}">{{ $option['option_text'] }}</span>
                                </label>
                            @endforeach
                        @elseif($currentQuestion['type'] === 'pg_kompleks')
                            @foreach($currentQuestion['options'] as $option)
                                <label wire:key="opsi-{{ $option['id'] }}"
                                    class="flex items-center space-x-4 p-4 border rounded-xl cursor-pointer transition-all duration-200 {{ isset($answers[$currentQuestion['id']]) && is_array($answers[$currentQuestion['id']]) && in_array($option['id'], $answers[$currentQuestion['id']]) ? 'bg-indigo-50 dark:bg-indigo-900/40 border-indigo-500 dark:border-indigo-400 ring-1 ring-indigo-500 dark:ring-indigo-400' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:border-gray-300 dark:hover:border-gray-600' }}">

                                    <input type="checkbox" name="jawaban_soal_{{ $currentQuestion['id'] }}[]"
                                        wire:model.live="answers.{{ $currentQuestion['id'] }}" value="{{ $option['id'] }}"
                                        class="w-5 h-5 rounded text-indigo-600 dark:text-indigo-400 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:bg-gray-800 dark:checked:bg-indigo-400">

                                    <span
                                        class="text-gray-700 dark:text-gray-200 font-medium text-lg {{ isset($answers[$currentQuestion['id']]) && is_array($answers[$currentQuestion['id']]) && in_array($option['id'], $answers[$currentQuestion['id']]) ? 'dark:text-indigo-100' : '' }}">{{ $option['option_text'] }}</span>
                                </label>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div
                    class="flex justify-between items-center pt-6 border-t border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    @if($currentQuestionIndex > 0)
                        <button wire:click="prevQuestion"
                            class="flex items-center gap-2 px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            &laquo; Sebelumnya
                        </button>
                    @else
                    <div></div> @endif

                    @if($currentQuestionIndex < count($questionsData) - 1)
                        <button wire:click="nextQuestion"
                            class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 dark:bg-indigo-500 text-white font-bold rounded-xl hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200 shadow-sm">
                            Selanjutnya &raquo;
                        </button>
                    @endif
                </div>

            </div>

            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 h-fit sticky top-28 transition-colors duration-300">

                    <h3
                        class="font-extrabold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700 pb-3 flex items-center gap-2 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 text-indigo-500 dark:text-indigo-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        Peta Soal
                    </h3>

                    <div class="grid grid-cols-5 gap-2 mb-8">
                        @foreach($questionsData as $index => $q)
                            @php
                                // Cek apakah soal ini sudah dijawab
                                $isAnswered = isset($answers[$q['id']]);
                                // Cek apakah ini adalah soal yang sedang dibuka
                                $isActive = $index === $currentQuestionIndex;
                            @endphp

                            <button wire:click="jumpToQuestion({{ $index }})" class="w-full aspect-square rounded-lg flex items-center justify-center text-sm font-bold transition-all duration-200
                                                                                    {{ $isActive ? 'ring-2 ring-offset-2 dark:ring-offset-gray-800 ring-indigo-500 transform scale-105' : '' }}
                                                                                    {{ $isAnswered ? 'bg-green-500 text-white border border-green-600 dark:border-green-500 shadow-inner' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}
                                                                                    ">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="w-4 h-4 bg-green-500 rounded-sm"></div> Sudah Dijawab
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <div
                                class="w-4 h-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-sm">
                            </div> Belum Dijawab
                        </div>
                    </div>

                    <button @click="showSubmitConfirm = true"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 focus:ring-4 focus:ring-emerald-200 dark:focus:ring-emerald-900 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
                        </svg>
                        Kumpulkan Ujian
                    </button>

                </div>
            </div>

        </div>
    @endif
</div>