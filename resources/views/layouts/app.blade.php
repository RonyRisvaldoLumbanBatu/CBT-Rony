<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: false }"
    x-init="
        darkMode = localStorage.getItem('darkMode') === 'true';
        $watch('darkMode', val => localStorage.setItem('darkMode', val))
    "
    :class="{ 'dark': darkMode }"
    @toggle-theme.window="darkMode = !darkMode">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // Anti-FOUC: Mencegah kedipan background putih saat mode gelap aktif
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>

    <title>{{ config('app.name', 'Sistem CBT') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* === DASAR DARK MODE === */
        /* Ubah warna latar belakang body jadi hitam pekat */
        .dark body {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
        }

        .dark .bg-gray-100 {
            background-color: #0f172a !important;
        }

        /* === PERBAIKAN KARTU (CARD) === */
        /* Semua elemen yang tadinya 'bg-white' atau 'bg-gray-50' diubah jadi abu-abu gelap */
        .dark .bg-white,
        .dark .bg-gray-50,
        .dark .bg-gray-50\/50,
        .dark .bg-gray-50\/80 {
            background-color: #1e293b !important;
            /* Warna kartu gelap */
            border-color: #334155 !important;
            /* Warna garis pinggir gelap */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3) !important;
            /* Bayangan halus */
        }

        /* === PERBAIKAN TEKS HANTU === */
        /* Paksa semua teks yang tadinya gelap menjadi terang */
        .dark .text-gray-900,
        .dark .text-gray-800,
        .dark .text-gray-700 {
            color: #f8fafc !important;
            /* Putih terang */
        }

        .dark .text-gray-600,
        .dark .text-gray-500,
        .dark .text-gray-400 {
            color: #cbd5e1 !important;
            /* Abu-abu terang */
        }

        /* === PERBAIKAN BORDER & INPUT === */
        .dark .border-gray-100,
        .dark .border-gray-200,
        .dark .border-gray-300 {
            border-color: #334155 !important;
        }

        .dark input,
        .dark textarea,
        .dark select {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: #f1f5f9 !important;
        }

        /* Warna placeholder input saat dark mode */
        .dark ::placeholder {
            color: #64748b !important;
            opacity: 1;
        }
    </style>
</head>

<body
    class="font-sans antialiased text-gray-900 bg-gray-100 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-screen">
        <livewire:layout.navigation />

        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow transition-colors duration-300">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>