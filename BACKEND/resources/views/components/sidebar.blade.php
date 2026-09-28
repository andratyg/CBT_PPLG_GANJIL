@props(['active' => ''])

<!-- LEFT SIDEBAR COMPONENT -->
<aside class="w-64 bg-[#0B132B] text-slate-300 min-h-screen flex flex-col justify-between py-6 px-4 shrink-0 fixed left-0 top-0 bottom-0 z-30 select-none">
    <div>
        <!-- Top Branding -->
        <div class="px-3 mb-8">
            <a href="{{ url('/admin') }}" class="block group">
                <h1 class="text-xl font-extrabold text-white tracking-tight leading-tight group-hover:text-blue-400 transition">GuruPortal</h1>
                <span class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mt-0.5 block">LMS INDONESIA</span>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-1.5">
            <!-- Presensi -->
            @php
                $isPresensiActive = ($active === 'presensi');
            @endphp
            <a href="{{ url('/admin/presensi') }}" class="{{ $isPresensiActive ? 'bg-[#1E293B] text-sky-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 font-medium' }} px-4 py-3 rounded-xl flex items-center gap-3.5 transition-colors shadow-xs">
                <svg class="w-5 h-5 {{ $isPresensiActive ? 'text-sky-400' : 'text-slate-400' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Presensi</span>
            </a>

            <!-- Materi -->
            @php
                $isMateriActive = ($active === 'materi');
            @endphp
            <a href="{{ url('/') }}" target="_blank" class="{{ $isMateriActive ? 'bg-[#1E293B] text-sky-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 font-medium' }} px-4 py-3 rounded-xl flex items-center gap-3.5 transition-colors">
                <svg class="w-5 h-5 {{ $isMateriActive ? 'text-sky-400' : 'text-slate-400' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
                <span class="text-sm">Materi</span>
            </a>

            <!-- Latihan & Tugas -->
            @php
                $isTugasActive = ($active === 'tugas');
            @endphp
            <a href="#tugas" class="{{ $isTugasActive ? 'bg-[#1E293B] text-sky-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 font-medium' }} px-4 py-3 rounded-xl flex items-center gap-3.5 transition-colors">
                <svg class="w-5 h-5 {{ $isTugasActive ? 'text-sky-400' : 'text-slate-400' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                <span class="text-sm">Latihan &amp; Tugas</span>
            </a>

            <!-- Kuis & Ujian -->
            @php
                $isKuisActive = ($active === 'kuis');
            @endphp
            <a href="#kuis" class="{{ $isKuisActive ? 'bg-[#1E293B] text-sky-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 font-medium' }} px-4 py-3 rounded-xl flex items-center gap-3.5 transition-colors">
                <svg class="w-5 h-5 {{ $isKuisActive ? 'text-sky-400' : 'text-slate-400' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm">Kuis &amp; Ujian</span>
            </a>

            <!-- Data -->
            @php
                $isMasterActive = ($active === 'master');
            @endphp
            <a href="{{ route('admin.master') }}" class="{{ $isMasterActive ? 'bg-[#1E293B] text-sky-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 font-medium' }} px-4 py-3 rounded-xl flex items-center gap-3.5 transition-colors">
                <svg class="w-5 h-5 {{ $isMasterActive ? 'text-sky-400' : 'text-slate-400' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                </svg>
                <span class="text-sm">Data</span>
            </a>

            <!-- Administrasi -->
            @php
                $isAdminActive = ($active === 'admin');
            @endphp
            <a href="#admin" class="{{ $isAdminActive ? 'bg-[#1E293B] text-sky-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 font-medium' }} px-4 py-3 rounded-xl flex items-center gap-3.5 transition-colors">
                <svg class="w-5 h-5 {{ $isAdminActive ? 'text-sky-400' : 'text-slate-400' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-sm">Administrasi</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Sidebar Actions (Logout) -->
    <div class="pt-4 border-t border-slate-800/80 px-2">
        <form action="{{ url('/logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full text-slate-400 hover:text-rose-400 hover:bg-slate-800/60 px-3 py-2.5 rounded-xl flex items-center gap-3 text-xs font-semibold transition cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </div>
</aside>
