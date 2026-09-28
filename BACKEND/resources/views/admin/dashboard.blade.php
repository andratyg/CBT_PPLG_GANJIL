<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Guru - GuruPortal LMS Indonesia</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F8FAFC;
        }
    </style>
</head>
<body class="min-h-screen bg-[#F8FAFC] text-slate-800 antialiased flex selection:bg-blue-100 selection:text-blue-700">

    <!-- SIDEBAR COMPONENT (Tanpa menu aktif yang terpilih saat pertama kali masuk) -->
    <x-sidebar />

    <!-- MAIN CONTENT AREA -->
    <main class="ml-64 flex-1 min-h-screen bg-[#F8FAFC] p-6 lg:p-10 flex flex-col justify-between">
        <div class="max-w-7xl w-full mx-auto space-y-8">

            <!-- Top Header: Greeting & Profile -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-[28px] font-extrabold text-slate-900 tracking-tight leading-tight">
                        Selamat Datang, {{ $guruNama ?? ' ' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                        Portal Guru &bull; Kelola kegiatan pembelajaran, presensi siswa, tugas, dan evaluasi kelas.
                    </p>
                </div>

                <!-- Teacher Profile Header -->
                <div class="flex items-center gap-3.5 self-start sm:self-auto bg-white sm:bg-transparent p-3 sm:p-0 rounded-2xl border sm:border-0 border-slate-100 shadow-xs sm:shadow-none">
                    <div class="text-right">
                        <div class="text-sm font-bold text-slate-800 leading-tight">
                            {{ $guruNama ?? 'Pak Andi Wijaya' }}
                        </div>
                        <div class="text-[11px] text-slate-400 font-medium mt-0.5">
                            NIP. {{ $guruNip ?? '198204122009031002' }}
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-[#3B82F6] text-white font-bold text-sm flex items-center justify-center shadow-xs">
                        AW
                    </div>
                </div>
            </div>

            <!-- Active Class Banner -->
            <div class="bg-gradient-to-r from-[#0B132B] to-[#1C2A4A] rounded-3xl p-6 sm:p-8 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                <!-- Soft Glow Backdrop -->
                <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-blue-500/20 blur-2xl pointer-events-none"></div>

                <div class="relative z-10 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold mb-3">
                        <span>Tahun Ajaran 2026/2027</span>
                        <span>&bull;</span>
                        <span>Semester Ganjil</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                        XI PPLG 1 &mdash; Pengembangan Perangkat Lunak &amp; Gim
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                        Mata Pelajaran: <strong>Basis Data &amp; Pemrograman Berorientasi Objek</strong>. Pilih modul di bawah atau buka menu di sidebar untuk mulai mengelola kelas.
                    </p>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row md:flex-col lg:flex-row items-stretch sm:items-center gap-3 shrink-0">
                    <a href="{{ url('/admin/presensi') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 active:scale-95 text-white font-semibold text-xs sm:text-sm shadow-sm transition">
                        <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Kelola Presensi Sekarang</span>
                    </a>
                </div>
            </div>

            <!-- Quick Summary Counters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Stat 1: Total Siswa -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Total Siswa</span>
                        <span class="text-2xl font-bold text-slate-800">32 Siswa</span>
                    </div>
                </div>

                <!-- Stat 2: Kehadiran Rata-rata -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Kehadiran Kelas</span>
                        <span class="text-2xl font-bold text-slate-800">92.5%</span>
                    </div>
                </div>

                <!-- Stat 3: Pertemuan Berjalan -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253 18.75m3-18.75h-18m18 0h-18" />
                            <circle cx="12" cy="13" r="3" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Pertemuan</span>
                        <span class="text-2xl font-bold text-slate-800">12 / 16</span>
                    </div>
                </div>

                <!-- Stat 4: Evaluasi / Kuis -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Kuis &amp; Ujian</span>
                        <span class="text-2xl font-bold text-slate-800">4 Paket</span>
                    </div>
                </div>
            </div>

            <!-- Main Module Quick Access Cards -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        MODUL &amp; MENU PEMBELAJARAN
                    </h4>
                    <span class="text-xs text-slate-400">Pilih modul di bawah atau klik menu pada sidebar</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Card 1: Presensi -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs hover:shadow-md hover:border-slate-300 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mb-4 transition-transform group-hover:scale-105">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                Kelola Presensi Siswa
                            </h5>
                            <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                                Catat dan pantau kehadiran siswa per pertemuan (Hadir, Izin, Sakit, Alpa, Dispensasi) dengan kalkulasi persentase otomatis.
                            </p>
                        </div>
                        <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-400 font-medium">Pertemuan 12 Aktif</span>
                            <a href="{{ url('/admin/presensi') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                                <span>Buka Presensi</span>
                                <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Card 2: Materi Pembelajaran -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs hover:shadow-md hover:border-slate-300 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4 transition-transform group-hover:scale-105">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                Bahan Ajar &amp; Materi
                            </h5>
                            <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                                Kelola modul belajar, modul digital PDF, video interaktif, dan panduan belajar untuk pembelajaran mandiri siswa.
                            </p>
                        </div>
                        <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-400 font-medium">7 Materi Tersedia</span>
                            <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                                <span>Lihat Portal Siswa</span>
                                <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Card 3: Latihan & Tugas -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs hover:shadow-md hover:border-slate-300 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 transition-transform group-hover:scale-105">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                Latihan &amp; Tugas
                            </h5>
                            <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                                Buat lembar kerja siswa, atur tenggat waktu penyerahan tugas, serta lakukan penilaian hasil pekerjaan siswa.
                            </p>
                        </div>
                        <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-400 font-medium">3 Tugas Aktif</span>
                            <a href="#tugas" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                                <span>Kelola Tugas</span>
                                <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Info -->
        <div class="pt-8 border-t border-slate-200/80 mt-12 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <div>
                &copy; 2026 GuruPortal LMS Indonesia &bull; Sistem CBT &amp; Manajemen Pembelajaran PPLG.
            </div>
            <div>
                Status Server: <span class="text-emerald-600 font-semibold">Online</span>
            </div>
        </div>
    </main>

</body>
</html>
