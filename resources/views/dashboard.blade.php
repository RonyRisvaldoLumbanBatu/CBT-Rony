<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight flex items-center gap-2 transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-indigo-600 dark:text-indigo-400"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
            Dasbor Siswa
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('sukses'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 dark:border-green-400 p-4 shadow-sm rounded-r-lg flex items-center gap-3 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600 dark:text-green-400"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <span class="text-green-700 dark:text-green-400 font-medium">{{ session('sukses') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-400 p-4 shadow-sm rounded-r-lg flex items-center gap-3 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600 dark:text-red-400"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span class="text-red-700 dark:text-red-400 font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    
                    <div>
                        <h3 class="text-xl font-extrabold mb-4 text-gray-800 dark:text-white flex items-center gap-2 transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-indigo-600 dark:text-indigo-400"><path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" /></svg>
                            Ujian Tersedia
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($ujianTersedia as $ujian)
                                <div class="bg-white dark:bg-gray-800 border border-indigo-100 dark:border-indigo-900/50 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-500 transition group flex flex-col">
                                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 group-hover:text-indigo-700 dark:group-hover:text-indigo-400 transition">{{ $ujian->title }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-4 flex-grow">{{ $ujian->description ?? 'Tidak ada deskripsi.' }}</p>
                                    
                                    <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-50 dark:border-gray-700">
                                        <span class="flex items-center gap-1.5 text-xs font-bold bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 px-3 py-1.5 rounded-lg border border-indigo-100 dark:border-indigo-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                            {{ $ujian->time_limit }} Menit
                                        </span>
                                        <a href="/ujian/{{ $ujian->id }}" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition shadow-sm flex items-center gap-1">
                                            Mulai <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full p-8 text-center flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-800/50 transition-colors duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada ujian baru yang tersedia.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-extrabold mb-4 text-gray-800 dark:text-white flex items-center gap-2 transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-emerald-600 dark:text-emerald-500"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" /></svg>
                            Riwayat Nilaimu
                        </h3>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
                            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                                <thead class="bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 text-xs uppercase text-gray-700 dark:text-gray-300 font-bold">
                                    <tr>
                                        <th class="px-6 py-4">Judul Ujian</th>
                                        <th class="px-6 py-4">Waktu Selesai</th>
                                        <th class="px-6 py-4 text-center">Nilai Akhir</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse($riwayatNilai as $riwayat)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition duration-200">
                                            <td class="px-6 py-4 font-bold text-gray-800 dark:text-gray-200">{{ $riwayat->exam->title }}</td>
                                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $riwayat->created_at->format('d M Y, H:i') }} WIB</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-extrabold {{ $riwayat->score >= 70 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                                    {{ $riwayat->score }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-400 dark:text-gray-500">Kamu belum mengerjakan ujian apa pun.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-amber-400 to-orange-500 dark:from-amber-600 dark:to-orange-700 rounded-xl shadow-md p-1 mb-6 h-fit sticky top-8 transition-colors duration-300">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 h-full transition-colors duration-300">
                            
                            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg shadow-inner">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.29 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" /></svg>
                                </div>
                                <h3 class="text-xl font-black text-gray-800 dark:text-gray-100 tracking-tight">Top 10 Global</h3>
                            </div>
                            
                            <div class="space-y-3">
                                @forelse($leaderboard as $index => $rank)
                                    <div class="flex items-center gap-3 p-3 rounded-xl border transition {{ $index === 0 ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : ($index === 1 ? 'bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700' : ($index === 2 ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800' : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50')) }}">
                                        
                                        <div class="flex items-center justify-center w-8 h-8 rounded-full font-black text-sm {{ $index === 0 ? 'bg-amber-400 text-white shadow-sm' : ($index === 1 ? 'bg-gray-300 text-gray-700 shadow-sm' : ($index === 2 ? 'bg-orange-400 text-white shadow-sm' : 'text-gray-400 dark:text-gray-500')) }}">
                                            {{ $index + 1 }}    
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">
                                                {{ $rank->user->name }}
                                                
                                                @if(auth()->id() === $rank->user_id)
                                                    <span class="ml-1 text-[10px] bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-1.5 py-0.5 rounded uppercase font-extrabold tracking-wider">Kamu</span>
                                                @endif
                                            </p>
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $rank->exam->title }}</p>
                                        </div>
                                        
                                        <div class="text-right">
                                            <span class="block text-lg font-black {{ $rank->score >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $rank->score }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-gray-400 dark:text-gray-500 text-sm">
                                        Belum ada skor yang tercatat. Jadilah yang pertama!
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>