<div class="max-w-7xl mx-auto py-6 sm:py-8 px-4 sm:px-6 lg:px-8 select-none" 
     x-data="{ 
         warnings: 0, 
         maxWarnings: 3,
         showWarning: false,
         showSubmitConfirm: false,
         isFullscreen: @entangle('isFullscreen'),
         fontSize: 18,

         toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    this.isFullscreen = true;
                }).catch(err => {
                    alert(`Gagal masuk mode layar penuh: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                    this.isFullscreen = false;
                }
            }
         },

         init() {
            document.addEventListener('contextmenu', event => event.preventDefault());
            document.addEventListener('copy', event => {
                event.preventDefault();
                alert('Tindakan menyalin tidak diizinkan!');
            });
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) this.triggerWarning('KELUAR DARI HALAMAN UJIAN');
            });
            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement && this.isFullscreen) {
                    this.isFullscreen = false;
                    this.triggerWarning('KELUAR DARI MODE LAYAR PENUH');
                }
            });
         },

         triggerWarning(reason) {
            if (this.showWarning) return;
            this.warnings++;
            this.showWarning = true;
            if (this.warnings >= this.maxWarnings) {
                if (document.fullscreenElement && document.exitFullscreen) document.exitFullscreen().catch(() => {});
                $wire.submitExam();
            } else {
                $wire.logCheatStrike(this.warnings);
            }
         }
     }">

    {{-- MODAL AREA --}}
    <!-- Peringatan Cheat -->
    <div x-show="showWarning" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/90 backdrop-blur-sm" style="display: none;" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-md w-full shadow-2xl border-2 border-red-500 text-center animate-bounce">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-2xl font-black text-red-600 mb-2 uppercase">Peringatan Keras!</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Anda terdeteksi keluar dari layar ujian! Pelanggaran: <strong class="text-xl" x-text="warnings"></strong>/<span x-text="maxWarnings"></span>.
            </p>
            <button @click="showWarning = false" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl uppercase">Saya Mengerti</button>
        </div>
    </div>

    <!-- Konfirmasi Kumpul -->
    <div x-show="showSubmitConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/90 backdrop-blur-sm" style="display: none;" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-sm w-full shadow-2xl text-center" @click.away="showSubmitConfirm = false">
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Selesai Ujian?</h3>
            <p class="text-gray-500 mb-6 text-sm">Pastikan semua jawaban sudah terisi dengan benar. Tindakan ini tidak bisa dibatalkan.</p>
            <div class="flex flex-col gap-3">
                <button wire:click="submitExam" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl uppercase">Ya, Kumpulkan</button>
                <button @click="showSubmitConfirm = false" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold py-3 rounded-xl">Batal</button>
            </div>
        </div>
    </div>

    @if(!$isPinVerified)
        {{-- LAYAR 1: INPUT PIN --}}
        <div class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 w-full max-w-md text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mb-6">
                    <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Ruang Ujian Terkunci</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Masukkan 6-Digit PIN Akses untuk memulai.</p>
                <form wire:submit.prevent="verifyPin" class="space-y-6">
                    <div>
                        <input type="text" wire:model.defer="inputPin" class="w-full text-center text-3xl font-black tracking-[0.3em] bg-gray-50 dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 rounded-xl py-4 uppercase focus:border-indigo-500 focus:ring-0" maxlength="6" required autofocus autocomplete="off">
                        @if(session()->has('pin_error')) 
                            <p class="text-red-500 font-bold text-sm mt-2">{{ session('pin_error') }}</p> 
                        @endif
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-lg py-4 rounded-xl shadow-md transition-colors">Validasi PIN</button>
                </form>
            </div>
        </div>
    @else
        {{-- LAYAR 2: KONFIRMASI FULLSCREEN --}}
        <div x-show="!isFullscreen" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border-2 border-amber-400 w-full max-w-lg text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100 mb-6">
                    <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Konfirmasi Ujian</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Ujian <strong>{{ $exam->title }}</strong> mewajibkan Mode Layar Penuh.</p>
                
                <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-xl mb-6 text-left border border-amber-200 dark:border-amber-800">
                    <p class="font-bold text-amber-800 dark:text-amber-500 mb-2 text-sm">Peraturan:</p>
                    <ul class="list-disc ml-5 space-y-1 text-sm text-amber-700 dark:text-amber-400">
                        <li>Dilarang keluar dari mode layar penuh.</li>
                        <li>Dilarang berpindah tab/aplikasi lain.</li>
                        <li>Melanggar aturan akan dicatat sebagai kecurangan.</li>
                    </ul>
                </div>

                <button @click="toggleFullscreen()" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold text-lg py-4 rounded-xl shadow-md transition-colors">MASUK LAYAR PENUH & MULAI</button>
            </div>
        </div>

        {{-- LAYAR 3: RUANG UJIAN --}}
        <div x-show="isFullscreen" style="display: none;" x-cloak>
            
            <!-- HEADER INFO -->
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-center sm:text-left">
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">{{ $exam->title }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Siswa: <span class="font-bold">{{ auth()->user()->name }}</span></p>
                </div>

                <div x-data="{ timeLeft: @entangle('timeLeft'), init() { setInterval(() => { if(this.timeLeft > 0) this.timeLeft--; else if(this.timeLeft === 0) $wire.submitExam(); }, 1000) }, formatTime(s) { let m=Math.floor(s/60); let sec=s%60; return m+':'+sec.toString().padStart(2,'0') } }" 
                     class="flex items-center gap-3 px-6 py-3 rounded-xl border"
                     :class="timeLeft <= 300 ? 'bg-red-50 border-red-200 dark:bg-red-900/30 dark:border-red-800' : 'bg-gray-50 border-gray-200 dark:bg-gray-900 dark:border-gray-700'">
                    <svg class="w-6 h-6" :class="timeLeft <= 300 ? 'text-red-500 animate-pulse' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div class="text-2xl font-mono font-bold" :class="timeLeft <= 300 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'" x-text="formatTime(timeLeft)"></div>
                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                <!-- KOLOM KIRI (SOAL) -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700" wire:key="soal-container-{{ $questionsData[$currentQuestionIndex]['id'] }}">
                        @php $currentQuestion = $questionsData[$currentQuestionIndex]; @endphp
                        
                        <!-- Header Soal -->
                        <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                            <span class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-4 py-1.5 rounded-lg font-bold text-sm">Soal Nomor {{ $currentQuestionIndex + 1 }}</span>
                            <div class="flex gap-2">
                                <button @click="fontSize = Math.max(14, fontSize - 2)" class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-200">A-</button>
                                <button @click="fontSize = Math.min(30, fontSize + 2)" class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-200">A+</button>
                            </div>
                        </div>

                        <!-- Teks Soal -->
                        <div class="mb-6">
                            <p class="text-gray-900 dark:text-gray-100 font-medium leading-relaxed" :style="`font-size: ${fontSize}px`">{{ $currentQuestion['question_text'] }}</p>
                        </div>

                        <!-- Gambar Soal -->
                        @if(!empty($currentQuestion['image_path']))
                            <div class="mb-6">
                                <img src="{{ Storage::url($currentQuestion['image_path']) }}" class="max-h-80 rounded-xl border dark:border-gray-700">
                            </div>
                        @endif
                        
                        <!-- Video Soal -->
                        @if(!empty($currentQuestion['youtube_url']))
                            <div class="mb-6 aspect-video w-full max-w-2xl rounded-xl overflow-hidden border dark:border-gray-700">
                                @php
                                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $currentQuestion['youtube_url'], $match);
                                    $youtubeId = $match[1] ?? null;
                                @endphp
                                @if($youtubeId)
                                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{ $youtubeId }}" frameborder="0" allowfullscreen></iframe>
                                @endif
                            </div>
                        @endif

                        <!-- Input Jawaban -->
                        <div class="space-y-3 mb-8">
                            @if($currentQuestion['type'] === 'essay')
                                <textarea wire:model.live="answers.{{ $currentQuestion['id'] }}" rows="5" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 p-4 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ketik jawaban essay Anda..."></textarea>
                            @elseif($currentQuestion['type'] === 'isian')
                                <input type="text" wire:model.live="answers.{{ $currentQuestion['id'] }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 p-4 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ketik jawaban singkat...">
                            @elseif(in_array($currentQuestion['type'], ['pg', 'benar_salah']))
                                @foreach($currentQuestion['options'] as $opt)
                                    <label wire:key="opsi-{{ $opt['id'] }}" class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-colors {{ (isset($answers[$currentQuestion['id']]) && $answers[$currentQuestion['id']] == $opt['id']) ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                        <input type="radio" name="jawaban_soal_{{ $currentQuestion['id'] }}" wire:model.live="answers.{{ $currentQuestion['id'] }}" value="{{ $opt['id'] }}" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-gray-700 dark:text-gray-200" :style="`font-size: ${fontSize-2}px`">{{ $opt['option_text'] }}</span>
                                    </label>
                                @endforeach
                            @elseif($currentQuestion['type'] === 'pg_kompleks')
                                @foreach($currentQuestion['options'] as $opt)
                                    <label wire:key="opsi-{{ $opt['id'] }}" class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-colors {{ (isset($answers[$currentQuestion['id']]) && is_array($answers[$currentQuestion['id']]) && in_array($opt['id'], $answers[$currentQuestion['id']])) ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                        <input type="checkbox" name="jawaban_soal_{{ $currentQuestion['id'] }}[]" wire:model.live="answers.{{ $currentQuestion['id'] }}" value="{{ $opt['id'] }}" class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-gray-700 dark:text-gray-200" :style="`font-size: ${fontSize-2}px`">{{ $opt['option_text'] }}</span>
                                    </label>
                                @endforeach
                            @endif
                        </div>

                        <!-- Tombol Navigasi Bawah Soal -->
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <button wire:click="prevQuestion" @if($currentQuestionIndex == 0) disabled @endif class="w-full sm:w-auto px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl disabled:opacity-50">
                                &laquo; Sebelumnya
                            </button>

                            <button wire:click="toggleDoubt" class="w-full sm:w-auto px-8 py-3 border-2 font-bold rounded-xl {{ in_array($currentQuestion['id'], $doubtfulQuestions) ? 'bg-yellow-100 border-yellow-400 text-yellow-700 dark:bg-yellow-900/30' : 'border-gray-300 text-gray-600 dark:border-gray-600 dark:text-gray-400' }}">
                                <input type="checkbox" class="mr-2 rounded text-yellow-500 focus:ring-yellow-500" @if(in_array($currentQuestion['id'], $doubtfulQuestions)) checked @endif> Ragu-ragu
                            </button>

                            @if($currentQuestionIndex < count($questionsData) - 1)
                                <button wire:click="nextQuestion" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md">
                                    Selanjutnya &raquo;
                                </button>
                            @else
                                <button @click="showSubmitConfirm = true" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md">
                                    Selesai Ujian
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN (PETA SOAL) -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 sticky top-6">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Navigasi Soal</h3>
                        
                        <div class="grid grid-cols-5 sm:grid-cols-8 lg:grid-cols-4 gap-2 mb-6">
                            @foreach($questionsData as $index => $q)
                                @php 
                                    $isAnswered = isset($answers[$q['id']]) && $answers[$q['id']] !== '' && $answers[$q['id']] !== [];
                                    $isActive = $index === $currentQuestionIndex;
                                    $isDoubt = in_array($q['id'], $doubtfulQuestions);
                                    
                                    $bgClass = 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700'; // Default Belum

                                    if ($isDoubt) {
                                        $bgClass = 'bg-yellow-400 text-yellow-900 border border-yellow-500 dark:border-yellow-600 shadow-inner'; // Ragu
                                    } elseif ($isAnswered) {
                                        $bgClass = 'bg-green-500 text-white border border-green-600 dark:border-green-500 shadow-inner'; // Dijawab & Yakin
                                    }
                                @endphp

                                <button wire:click="jumpToQuestion({{ $index }})" class="w-full aspect-square rounded-lg flex items-center justify-center text-sm font-bold transition-all duration-200
                                                                                                                          {{ $isActive ? 'ring-2 ring-offset-2 dark:ring-offset-gray-800 ring-indigo-500 transform scale-105' : '' }}
                                                                                                                          {{ $bgClass }}">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>

                        <div class="space-y-3 mb-6 text-sm">
                            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-green-500 rounded-sm"></div> <span class="text-gray-600 dark:text-gray-400 font-medium">Sudah Dijawab</span></div>
                            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-yellow-400 rounded-sm border border-yellow-500"></div> <span class="text-gray-600 dark:text-gray-400 font-medium">Ragu-ragu</span></div>
                            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-sm"></div> <span class="text-gray-600 dark:text-gray-400 font-medium">Belum Dijawab</span></div>
                        </div>

                        <button @click="showSubmitConfirm = true" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm">
                            Kumpulkan Ujian
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>