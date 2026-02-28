<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UjianPintar') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans text-slate-900 dark:text-slate-100 antialiased bg-slate-50 dark:bg-slate-900 selection:bg-teal-500 selection:text-white transition-colors duration-300">
    <div class="min-h-screen flex flex-col justify-center items-center p-4">

        <div class="mb-8">
            <a href="/" wire:navigate class="flex items-center gap-3 group">
                <div
                    class="p-3 bg-teal-600 rounded-2xl group-hover:bg-teal-700 group-hover:-translate-y-1 transition-all duration-300 shadow-lg shadow-teal-600/30">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-10 h-10 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                </div>
                <span class="font-black text-4xl tracking-tight text-slate-800 dark:text-white">
                    Ujian<span class="text-teal-600 dark:text-teal-400">Pintar</span>
                </span>
            </a>
        </div>

        <div
            class="w-full sm:max-w-md bg-white dark:bg-slate-800 shadow-2xl overflow-hidden rounded-3xl border border-slate-100 dark:border-slate-700 p-8 sm:p-10 transition-colors duration-300">
            {{ $slot }}
        </div>

        <p class="mt-8 text-sm text-slate-500 dark:text-slate-400">
            &copy; {{ date('Y') }} UjianPintar. All rights reserved.
        </p>
    </div>
</body>

</html>