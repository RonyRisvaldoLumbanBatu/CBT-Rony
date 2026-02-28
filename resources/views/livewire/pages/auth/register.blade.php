<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Default register as 'siswa'
        $validated['role'] = 'siswa';

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Buat Akun Baru</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Daftar sekarang untuk mulai mengerjakan ujian online
            dengan mudah.</p>
    </div>

    <form wire:submit.prevent="register" class="space-y-5">

        <div>
            <label for="name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama
                Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input wire:model="name" id="name" type="text"
                    class="block w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-xl leading-5 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-teal-500 dark:focus:border-teal-400 transition sm:text-sm"
                    placeholder="John Doe" required autofocus autocomplete="name">
            </div>
            @error('name') <span
            class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
        </div>


        <div>
            <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat
                Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input wire:model="email" id="email" type="email"
                    class="block w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-xl leading-5 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-teal-500 dark:focus:border-teal-400 transition sm:text-sm"
                    placeholder="john@example.com" required autocomplete="username">
            </div>
            @error('email') <span
            class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
        </div>


        <div>
            <label for="password"
                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input wire:model="password" id="password" type="password"
                    class="block w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-xl leading-5 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-teal-500 dark:focus:border-teal-400 transition sm:text-sm"
                    placeholder="••••••••" required autocomplete="new-password">
            </div>
            @error('password') <span
            class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
        </div>


        <div>
            <label for="password_confirmation"
                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <input wire:model="password_confirmation" id="password_confirmation" type="password"
                    class="block w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-xl leading-5 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-teal-500 dark:focus:border-teal-400 transition sm:text-sm"
                    placeholder="••••••••" required autocomplete="new-password">
            </div>
            @error('password_confirmation') <span
            class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors uppercase tracking-widest gap-2 items-center">
                <span wire:loading.remove>Daftar Sekarang</span>
                <span wire:loading class="animate-pulse">Mendaftarkan...</span>
                <svg wire:loading.remove class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                    </path>
                </svg>
            </button>
        </div>

        <p class="mt-4 text-center text-sm text-slate-500 dark:text-slate-400">
            Sudah punya akun?
            <a href="{{ route('login') }}" wire:navigate
                class="font-bold text-teal-600 hover:text-teal-500 dark:text-teal-400 dark:hover:text-teal-300 transition-colors">
                Masuk di sini
            </a>
        </p>

    </form>
</div>