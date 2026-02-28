<div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8 transition-colors duration-300">
    
    <a href="/guru/ujian" class="inline-flex items-center gap-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 font-semibold mb-6 transition group">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 group-hover:-translate-x-1 transition-transform">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Kembali ke Dashboard Guru
    </a>

    <div class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 transition-colors duration-300">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-100 dark:border-indigo-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-indigo-600 dark:text-indigo-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.651a3.75 3.75 0 0 1 0-5.303m5.304 0a3.75 3.75 0 0 1 0 5.303m-7.425 2.122a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M3.75 20.25A12 75 12 75 0 0 1 3.75 3.75m16.5 0a12 75 12 75 0 0 1 0 16.5" />
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white tracking-tight">Radar Pengawas: {{ $exam->title }}</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Merekam aktivitas siswa secara real-time.</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300">
        <div class="flex items-center gap-2 mb-6 border-b dark:border-gray-700 pb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-gray-500">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Log Aktivitas Siswa</h3>
        </div>

        <div class="space-y-3">
            @forelse($logs as $log)
                <div class="group p-4 bg-gray-50/50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-xl flex flex-col sm:flex-row sm:justify-between sm:items-center hover:bg-indigo-50/30 dark:hover:bg-indigo-900/40 hover:border-indigo-100 dark:hover:border-indigo-800 transition duration-300">
                    <div class="flex items-center gap-3 mb-2 sm:mb-0">
                        <div class="bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/60 group-hover:border-indigo-200 dark:group-hover:border-indigo-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-indigo-500 dark:text-indigo-400">
                              <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-gray-800 dark:text-gray-100 text-base">{{ $log['nama'] }}</span>
                            <span class="text-gray-500 dark:text-gray-400 ml-1 text-sm">{{ $log['pesan'] }}</span>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm transition-colors duration-300">
                        {{ $log['waktu'] }}
                    </span>
                </div>
            @empty
                <div class="p-12 text-center flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium text-lg">Radar Siaga</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Menunggu aktivitas masuk. Log akan otomatis muncul di sini saat siswa mulai ujian.</p>
                </div>
            @endempty
        </div>
    </div>
</div>