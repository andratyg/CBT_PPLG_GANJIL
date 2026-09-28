@props(['active' => 'materi'])

<nav class="w-full bg-white border-b border-slate-200/80 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo & Subject -->
            <a href="{{ url('/') }}" class="flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100/80 flex items-center justify-center text-blue-600 shadow-xs transition-transform group-hover:scale-105">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-bold text-slate-900 leading-tight tracking-tight">Bahasa Indonesia</span>
                    <span class="text-[11px] font-bold tracking-wider text-blue-600 uppercase mt-0.5">SMK Wikrama Bogor</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <div class="hidden md:flex items-center space-x-8 h-20">
                <a href="{{ url('/') }}" class="h-20 inline-flex items-center text-sm transition {{ $active === 'materi' ? 'font-semibold text-blue-600 border-b-2 border-blue-600' : 'font-medium text-slate-600 hover:text-slate-900' }}">
                    Materi
                </a>
                <a href="{{ url('/tugas') }}" class="h-20 inline-flex items-center text-sm transition {{ $active === 'tugas' ? 'font-semibold text-blue-600 border-b-2 border-blue-600' : 'font-medium text-slate-600 hover:text-slate-900' }}">
                    Latihan &amp; Tugas
                </a>
                <a href="#kuis" class="h-20 inline-flex items-center text-sm transition {{ $active === 'kuis' ? 'font-semibold text-blue-600 border-b-2 border-blue-600' : 'font-medium text-slate-600 hover:text-slate-900' }}">
                    Kuis &amp; Ujian
                </a>
                <a href="#nilai" class="h-20 inline-flex items-center text-sm transition {{ $active === 'nilai' ? 'font-semibold text-blue-600 border-b-2 border-blue-600' : 'font-medium text-slate-600 hover:text-slate-900' }}">
                    Nilai Saya
                </a>
            </div>

            <!-- Right Actions (Divider & Login Guru Button) -->
            <div class="hidden md:flex items-center gap-4">
                <div class="h-6 w-px bg-slate-200"></div>
                <a href="{{ url('/login') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 transition shadow-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <span>Login Guru</span>
                </a>
            </div>

            <!-- Mobile Menu Toggle Button -->
            <div class="flex items-center md:hidden gap-3">
                <a href="{{ url('/login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-medium text-slate-700 bg-white hover:bg-slate-50">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <span>Login</span>
                </a>
                <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 py-3 space-y-1">
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-base {{ $active === 'materi' ? 'font-semibold text-blue-600 bg-blue-50/60' : 'font-medium text-slate-700 hover:bg-slate-50' }}">
                Materi
            </a>
            <a href="{{ url('/tugas') }}" class="block px-3 py-2 rounded-md text-base {{ $active === 'tugas' ? 'font-semibold text-blue-600 bg-blue-50/60' : 'font-medium text-slate-700 hover:bg-slate-50' }}">
                Latihan &amp; Tugas
            </a>
            <a href="#kuis" class="block px-3 py-2 rounded-md text-base {{ $active === 'kuis' ? 'font-semibold text-blue-600 bg-blue-50/60' : 'font-medium text-slate-700 hover:bg-slate-50' }}">
                Kuis &amp; Ujian
            </a>
            <a href="#nilai" class="block px-3 py-2 rounded-md text-base {{ $active === 'nilai' ? 'font-semibold text-blue-600 bg-blue-50/60' : 'font-medium text-slate-700 hover:bg-slate-50' }}">
                Nilai Saya
            </a>
        </div>
    </div>
</nav>
