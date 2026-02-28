<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

// Kita beri tahu Livewire untuk memakai layout kosong yang baru kita buat
new #[Layout('layouts.blank')] class extends Component {
    public LoginForm $form;

    // Otak proses loginnya kembali!
    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex min-h-screen">

    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-slate-900 items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-br from-teal-600 via-teal-800 to-slate-900 opacity-90"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-white opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-72 h-72 bg-teal-400 opacity-20 rounded-full blur-2xl"></div>

        <div class="relative z-10 p-12 text-center text-white">
            <div class="flex justify-center mb-6">
                <div class="p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-16 h-16 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                </div>
            </div>
            <h1 class="text-4xl font-extrabold tracking-tight mb-4 text-white">Sistem Ujian Pintar</h1>
            <p class="text-lg text-teal-50 font-medium max-w-md mx-auto leading-relaxed">
                Evaluasi belajar yang jujur, cepat, dan aman. Dilengkapi dengan Radar Pengawas Real-time dan Pengacak
                Soal otomatis.
            </p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 bg-slate-50">
        <div class="w-full max-w-md">

            <div class="text-center lg:text-left mb-10">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang! 👋</h2>
                <p class="text-slate-500 mt-2 font-medium">Silakan masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            @error('form.email')
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex items-center gap-3 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-red-600 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-red-700 text-sm font-semibold">{{ $message }}</span>
                </div>
            @enderror

            <form wire:submit="login" class="space-y-6">

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-5 h-5 text-slate-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input wire:model="form.email" id="email" type="email" required autofocus
                            class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-sm placeholder-slate-400"
                            placeholder="guru@ujian.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-5 h-5 text-slate-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <input wire:model="form.password" id="password" type="password" required
                            class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-sm placeholder-slate-400"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="flex items-center cursor-pointer group">
                        <input wire:model="form.remember" id="remember_me" type="checkbox"
                            class="w-5 h-5 rounded border-slate-300 text-teal-600 shadow-sm focus:ring-teal-500 transition cursor-pointer">
                        <span class="ml-2 text-sm text-slate-600 font-medium group-hover:text-teal-600 transition">Ingat
                            Saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all active:scale-[0.98]">
                    <span wire:loading.remove>Masuk ke Dasbor</span>
                    <span wire:loading class="animate-pulse">Sedang Log In...</span>
                    <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>

            <p class="mt-8 relative text-center text-sm text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" wire:navigate
                    class="font-bold text-teal-600 hover:text-teal-500 transition-colors">
                    Daftar Bebas Biaya
                </a>
            </p>

        </div>
    </div>
</div>