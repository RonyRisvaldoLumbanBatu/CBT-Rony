<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ cbt_name() }} - {{ cbt_institution() }}</title>
        @if(cbt_logo_url())
            <link rel="icon" href="{{ cbt_logo_url() }}">
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Poppins', sans-serif; }
            /* Background lokal (tanpa gambar eksternal) supaya halaman tetap
               tampil benar saat lab sekolah offline. Admin bisa mengganti
               dengan banner sendiri lewat Panel Admin. */
            .bg-portal {
                @if(cbt_banner_url())
                    background: url('{{ cbt_banner_url() }}') center / cover no-repeat fixed;
                @else
                    background:
                        radial-gradient(ellipse 80% 60% at 20% 10%, rgba(45, 212, 191, 0.25), transparent 60%),
                        radial-gradient(ellipse 70% 60% at 85% 90%, rgba(99, 102, 241, 0.3), transparent 60%),
                        linear-gradient(160deg, #0f172a 0%, #1e293b 55%, #134e4a 100%);
                    background-attachment: fixed;
                @endif
            }
        </style>
    </head>
    <body class="antialiased min-h-screen bg-portal flex items-center justify-center p-4">
        
        <!-- Overlay Gelap agar background gambar tidak bertabrakan dengan teks -->
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm z-0"></div>

        <div class="relative z-10 w-full max-w-md">
            
            <div class="bg-white/10 dark:bg-slate-900/60 backdrop-blur-md rounded-3xl p-8 shadow-2xl border border-white/20 dark:border-slate-700/50 text-center transition-colors duration-300">
                
                <!-- Logo Sekolah / Sistem (bisa diganti admin lewat Panel Admin) -->
                <div class="flex justify-center mb-6">
                    @if(cbt_logo_url())
                        <img src="{{ cbt_logo_url() }}" alt="Logo {{ cbt_institution() }}"
                            class="w-20 h-20 object-contain rounded-2xl bg-white p-2 shadow-lg">
                    @else
                        <div class="p-4 bg-teal-600 rounded-2xl shadow-lg shadow-teal-600/30">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <h1 class="text-3xl font-black text-white tracking-tight mb-2">
                    {{ cbt_name() }}
                </h1>
                <p class="text-teal-100/80 mb-2 font-medium text-sm">
                    Portal Evaluasi Akademik &amp; Ujian {{ term('sekolah') }}
                </p>
                <p class="text-white/90 mb-8 font-bold text-sm">
                    {{ cbt_institution() }}
                    @if(app_setting('academic_year'))
                        <span class="block text-xs font-medium text-teal-100/70 mt-1">Tahun Ajaran {{ app_setting('academic_year') }}</span>
                    @endif
                </p>

                <div class="space-y-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 transition">
                            Masuk ke Dashboard
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-slate-900 bg-white hover:bg-slate-50 transition active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                            Login Akun
                        </a>

                        <p class="text-xs text-white/60 leading-relaxed pt-1">
                            Belum punya akun? Akun {{ strtolower(term('siswa')) }} dan {{ strtolower(term('guru')) }}
                            dibuatkan oleh administrator {{ strtolower(term('sekolah')) }}.
                        </p>
                    @endauth
                </div>

            </div>

            <div class="mt-8 text-center text-sm text-slate-400">
                &copy; {{ date('Y') }} {{ cbt_institution() }}<br>
                <span class="text-xs opacity-70">Tim IT & Pengembangan Akademik</span>
            </div>

        </div>

    </body>
</html>
