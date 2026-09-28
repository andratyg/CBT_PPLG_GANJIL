<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Latihan & Tugas Mandiri - SMK Wikrama BOGOR</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite & CDN Fallback) -->
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
            background-color: #f8fafc;
        }
        [x-cloak] { display: none !important; }

        @keyframes modalPop {
            0% {
                opacity: 0;
                transform: scale(0.94) translateY(14px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        .animate-modal-pop {
            animation: modalPop 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Custom sleek scrollbar for modals & candidate lists */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#f8fafc] text-slate-800 antialiased selection:bg-blue-100 selection:text-blue-700">

    <!-- Top Navigation Bar (Active: Latihan & Tugas) -->
    <x-navbar active="tugas" />

    <!-- Main Content Container -->
    <main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Daftar Latihan &amp; Tugas Mandiri
            </h1>
            <p class="mt-2 text-sm sm:text-base text-slate-500 max-w-3xl leading-relaxed">
                Asah pemahaman sastra dan tata bahasa Indonesia. Selesaikan pengumpulan tugas tepat waktu secara mandiri.
            </p>
        </div>

        <!-- ========================================================================= -->
        <!-- ACTIVE STUDENT IDENTITY BANNER (DIPILIH DARI POP-UP ROMBEL & NAMA)        -->
        <!-- ========================================================================= -->
        <div id="active-student-banner" class="mb-8 p-4 sm:p-5 bg-gradient-to-r from-blue-50/90 via-sky-50/50 to-white border border-blue-200/80 rounded-2xl flex flex-wrap items-center justify-between gap-4 shadow-xs transition-all">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-base shadow-sm shrink-0 tracking-wider">
                    <span id="banner-student-initial">AF</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span id="banner-student-rombel" class="text-[11px] font-bold uppercase tracking-wider text-blue-700 bg-blue-100/80 px-2.5 py-0.5 rounded-md">PPLG X-3</span>
                        <span id="banner-student-absen" class="text-xs text-slate-500 font-medium">Absen 02</span>
                    </div>
                    <h3 id="banner-student-name" class="text-base sm:text-lg font-bold text-slate-900 leading-tight mt-1">
                        Ahmad Fauzi
                    </h3>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openIdentityModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-blue-50/60 border border-slate-200/90 text-slate-700 hover:text-blue-600 hover:border-blue-300 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Ganti Rombel / Nama</span>
                </button>
            </div>
        </div>

        <!-- Task Cards Container -->
        <div class="space-y-6">

            <!-- ========================================== -->
            <!-- CARD 1: TUGAS AKTIF (2 HARI LAGI)          -->
            <!-- ========================================== -->
            <div id="card-tugas-1" class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-7 shadow-xs transition-all duration-300">
                <!-- Top Meta Row -->
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/70 text-emerald-600 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            2 Hari Lagi
                        </span>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wide">
                            BAB I &mdash; LHO
                        </span>
                    </div>
                    <span class="text-xs sm:text-sm text-slate-400 font-medium">
                        Tenggat: Besok, 23:59 WIB
                    </span>
                </div>

                <!-- Task Title -->
                <h2 class="text-base sm:text-lg font-bold text-slate-900 mt-4 leading-snug">
                    Tugas Mandiri: Menganalisis Teks Laporan Hasil Observasi Lingkungan Sekitar
                </h2>

                <!-- INITIAL STATE: Button Kumpulkan Tugas -->
                <div id="card-1-initial-action" class="flex items-center justify-end mt-8 pt-4 border-t border-slate-100">
                    <button type="button" onclick="handleStartSubmission(1)" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                        Kumpulkan Tugas
                    </button>
                </div>

                <!-- UPLOAD STATE: (Sesuai Gambar 'Latihan & Tugas — Unggah Jawaban.png' & 'Error Berkas Wajib.png') -->
                <div id="card-1-upload-section" class="hidden mt-5">
                    <!-- Student Identity Pill Bar -->
                    <div class="px-4 py-3 bg-slate-50 border border-slate-200/80 rounded-xl flex flex-wrap items-center justify-between gap-3 text-xs sm:text-sm text-slate-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Kamu mengumpulkan dengan nama: <strong id="card-1-student-name" class="text-blue-600 font-bold">Ahmad Fauzi (Absen 02)</strong> &mdash; Rombel <strong id="card-1-student-rombel" class="text-blue-600 font-bold">PPLG X-3</strong></span>
                        </div>
                        <button type="button" onclick="openIdentityModal(1)" class="text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-semibold hover:underline cursor-pointer">
                            Ganti Siswa
                        </button>
                    </div>

                    <!-- Dropzone Area -->
                    <div id="card-1-dropzone" onclick="triggerFileInput(1)" ondragover="handleDragOver(event, 1)" ondragleave="handleDragLeave(event, 1)" ondrop="handleDrop(event, 1)" class="mt-4 border-2 border-dashed border-blue-400 bg-blue-50/15 rounded-2xl py-12 px-6 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-blue-50/30 transition group select-none relative">
                        <input type="file" id="file-input-1" class="hidden" accept=".pdf,.docx,.zip" onchange="handleFileSelected(1)" />
                        
                        <!-- Icon & Text Container -->
                        <div id="card-1-dropzone-content" class="flex flex-col items-center">
                            <!-- Cloud Icon (Normal) -->
                            <div id="card-1-dropzone-icon" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 mb-2 transition group-hover:scale-110">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                            </div>

                            <p id="card-1-dropzone-text" class="text-sm font-bold text-slate-800">
                                Tarik file jawaban ke sini atau klik untuk pilih
                            </p>
                            <p class="text-xs text-slate-400 mt-1">
                                Format yang didukung: PDF, DOCX, atau ZIP hingga 10MB
                            </p>
                        </div>

                        <!-- Selected File Preview Container -->
                        <div id="card-1-file-preview" class="hidden flex items-center gap-3 bg-white border border-emerald-200 py-2.5 px-4 rounded-xl shadow-xs">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-left">
                                <p id="card-1-file-name" class="text-xs sm:text-sm font-bold text-slate-800 truncate max-w-xs sm:max-w-md"></p>
                                <p id="card-1-file-size" class="text-[11px] text-slate-400"></p>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); resetFileUpload(1);" class="ml-2 text-slate-400 hover:text-rose-500 p-1 rounded-lg">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Action & Error Row -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-4">
                        <!-- Left Status Text / Error Message -->
                        <div>
                            <!-- Normal hint -->
                            <p id="card-1-hint-text" class="text-xs text-slate-400">
                                Pastikan nama dan berkas Anda sudah benar sebelum mengumpulkan.
                            </p>
                            <!-- Error text (Sesuai Gambar 'Latihan & Tugas — Error Berkas Wajib.png') -->
                            <p id="card-1-error-text" class="hidden text-xs sm:text-sm text-rose-600 font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                Berkas wajib diunggah sebelum mengumpulkan
                            </p>
                        </div>

                        <!-- Right Submit Button -->
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="cancelUpload(1)" class="px-4 py-2.5 text-xs font-semibold text-slate-500 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button" id="card-1-submit-btn" onclick="submitTugas(1)" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                                Kumpulkan Tugas
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SUBMITTED STATE: Setelah Berhasil Dikumpulkan -->
                <div id="card-1-submitted-action" class="hidden flex items-center justify-end mt-8 pt-4 border-t border-slate-100">
                    <button type="button" onclick="viewSubmission(1, 'Tugas Mandiri: Menganalisis Teks Laporan Hasil Observasi Lingkungan Sekitar')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition cursor-pointer">
                        Lihat Pengumpulan
                    </button>
                </div>
            </div>


            <!-- ========================================== -->
            <!-- CARD 2: TUGAS TERLAMBAT                   -->
            <!-- ========================================== -->
            <div id="card-tugas-2" class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-7 shadow-xs transition-all duration-300">
                <!-- Top Meta Row -->
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200/70 text-rose-600 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            Terlambat
                        </span>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wide">
                            BAB II &mdash; Eksposisi
                        </span>
                    </div>
                    <span class="text-xs sm:text-sm text-slate-400 font-medium">
                        Tenggat: 3 Hari yang lalu, 18:00 WIB
                    </span>
                </div>

                <!-- Task Title -->
                <h2 class="text-base sm:text-lg font-bold text-slate-900 mt-4 leading-snug">
                    Latihan Struktur Kebahasaan: Membedakan Opini dan Fakta dalam Teks Eksposisi
                </h2>

                <!-- INITIAL STATE: Pesan & Tombol Kumpulkan Terlambat -->
                <div id="card-2-initial-action" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-8 pt-4 border-t border-slate-100">
                    <p class="text-xs sm:text-sm text-rose-500 font-normal">
                        Pengumpulan setelah deadline akan ditandai terlambat
                    </p>
                    <button type="button" onclick="handleStartSubmission(2)" class="px-5 py-2.5 border border-rose-500 text-rose-600 hover:bg-rose-50 text-sm font-semibold rounded-xl transition cursor-pointer">
                        Kumpulkan Terlambat
                    </button>
                </div>

                <!-- UPLOAD STATE UNTUK CARD 2 -->
                <div id="card-2-upload-section" class="hidden mt-5">
                    <div class="px-4 py-3 bg-slate-50 border border-slate-200/80 rounded-xl flex flex-wrap items-center justify-between gap-3 text-xs sm:text-sm text-slate-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Kamu mengumpulkan dengan nama: <strong id="card-2-student-name" class="text-blue-600 font-bold">Ahmad Fauzi (Absen 02)</strong> &mdash; Rombel <strong id="card-2-student-rombel" class="text-blue-600 font-bold">PPLG X-3</strong></span>
                        </div>
                        <button type="button" onclick="openIdentityModal(2)" class="text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-semibold hover:underline cursor-pointer">
                            Ganti Siswa
                        </button>
                    </div>

                    <!-- Dropzone Area 2 -->
                    <div id="card-2-dropzone" onclick="triggerFileInput(2)" ondragover="handleDragOver(event, 2)" ondragleave="handleDragLeave(event, 2)" ondrop="handleDrop(event, 2)" class="mt-4 border-2 border-dashed border-rose-300 bg-rose-50/15 rounded-2xl py-12 px-6 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-rose-50/30 transition group select-none relative">
                        <input type="file" id="file-input-2" class="hidden" accept=".pdf,.docx,.zip" onchange="handleFileSelected(2)" />
                        
                        <div id="card-2-dropzone-content" class="flex flex-col items-center">
                            <div id="card-2-dropzone-icon" class="w-10 h-10 rounded-full flex items-center justify-center text-rose-500 mb-2 transition group-hover:scale-110">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                            </div>
                            <p id="card-2-dropzone-text" class="text-sm font-bold text-slate-800">
                                Tarik file jawaban ke sini atau klik untuk pilih
                            </p>
                            <p class="text-xs text-slate-400 mt-1">
                                Format yang didukung: PDF, DOCX, atau ZIP hingga 10MB
                            </p>
                        </div>

                        <div id="card-2-file-preview" class="hidden flex items-center gap-3 bg-white border border-emerald-200 py-2.5 px-4 rounded-xl shadow-xs">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-left">
                                <p id="card-2-file-name" class="text-xs sm:text-sm font-bold text-slate-800 truncate max-w-xs sm:max-w-md"></p>
                                <p id="card-2-file-size" class="text-[11px] text-slate-400"></p>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); resetFileUpload(2);" class="ml-2 text-slate-400 hover:text-rose-500 p-1 rounded-lg">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Action & Error Row 2 -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-4">
                        <div>
                            <p id="card-2-hint-text" class="text-xs text-rose-500 font-medium">
                                Catatan: Pengumpulan tugas ini akan ditandai terlambat di laporan guru.
                            </p>
                            <p id="card-2-error-text" class="hidden text-xs sm:text-sm text-rose-600 font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                Berkas wajib diunggah sebelum mengumpulkan
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="cancelUpload(2)" class="px-4 py-2.5 text-xs font-semibold text-slate-500 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button" id="card-2-submit-btn" onclick="submitTugas(2)" class="px-5 py-2.5 border border-rose-500 bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                                Kumpulkan Terlambat
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SUBMITTED STATE 2 -->
                <div id="card-2-submitted-action" class="hidden flex items-center justify-end mt-8 pt-4 border-t border-slate-100">
                    <button type="button" onclick="viewSubmission(2, 'Latihan Struktur Kebahasaan: Membedakan Opini dan Fakta dalam Teks Eksposisi')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition cursor-pointer">
                        Lihat Pengumpulan
                    </button>
                </div>
            </div>


            <!-- ========================================== -->
            <!-- CARD 3: SUDAH DIKUMPULKAN                  -->
            <!-- ========================================== -->
            <div id="card-tugas-3" class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-7 shadow-xs transition-all duration-300">
                <!-- Top Meta Row -->
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-600 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Sudah Dikumpulkan
                        </span>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wide">
                            BAB II &mdash; Eksposisi
                        </span>
                    </div>
                    <span class="text-xs sm:text-sm text-slate-400 font-medium">
                        Tenggat: 12 Okt 2024, 23:59 WIB
                    </span>
                </div>

                <!-- Task Title -->
                <h2 class="text-base sm:text-lg font-bold text-slate-900 mt-4 leading-snug">
                    Menulis Gagasan Kritis Mengenai Isu Sosial di Media Massa Setempat
                </h2>

                <!-- Action Button: Lihat Pengumpulan -->
                <div class="flex items-center justify-end mt-8 pt-4 border-t border-slate-100">
                    <button type="button" onclick="viewSubmission(3, 'Menulis Gagasan Kritis Mengenai Isu Sosial di Media Massa Setempat')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition cursor-pointer">
                        Lihat Pengumpulan
                    </button>
                </div>
            </div>

        </div>

    </main>


    <!-- ========================================================================= -->
    <!-- MODAL: PILIH ROMBEL & IDENTITAS (PREMIUM REDESIGN WITH RICH AESTHETICS)   -->
    <!-- ========================================================================= -->
    <div id="modal-identity" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div class="relative bg-white rounded-[24px] sm:rounded-[28px] p-5 sm:p-7 max-w-lg sm:max-w-xl w-full shadow-[0_25px_70px_-15px_rgba(15,23,42,0.35)] border border-slate-100/90 my-auto max-h-[92vh] overflow-y-auto animate-modal-pop">
            
            <!-- Top Ambient Glow Gradient Deco -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent rounded-full opacity-80"></div>

            <!-- Circular Close Button in Top Right Corner -->
            <button type="button" onclick="closeIdentityModal()" aria-label="Tutup" class="absolute top-4 right-4 sm:top-5 sm:right-5 w-8 h-8 rounded-full bg-slate-100/80 hover:bg-slate-200 border border-slate-200/60 text-slate-500 hover:text-slate-900 transition-all flex items-center justify-center cursor-pointer shadow-xs hover:scale-105 active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="pr-8">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-[10px] font-bold tracking-wide uppercase mb-1">
                    <span>Pintu Masuk Siswa</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-snug">
                    Pilih Identitas Siswa
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                    Pilih rombel dan nama Anda untuk mengakses dashboard dan mengumpulkan tugas.
                </p>
            </div>

            <!-- STEP 1: PILIH ROMBEL (MODERN GRID CARDS) -->
            <div class="mt-4 sm:mt-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold tracking-wider text-slate-500 uppercase flex items-center gap-1.5">
                        <span class="w-4.5 h-4.5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center">1</span>
                        <span>Pilih Rombel / Kelas</span>
                    </span>
                    <span id="selected-rombel-indicator" class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200/60">
                        PPLG X-3
                    </span>
                </div>
                
                <!-- Rombel Grid Container -->
                <div id="rombelListContainer" class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-36 sm:max-h-40 overflow-y-auto pr-1">
                    @forelse($daftarRombel as $index => $rombel)
                        <button type="button" id="rombel-opt-{{ $index }}" onclick="selectRombel('{{ $rombel }}', 'rombel-opt-{{ $index }}')" class="rombel-item group p-2.5 rounded-xl border text-left transition-all cursor-pointer {{ $index === 0 ? 'bg-gradient-to-br from-blue-50 to-indigo-50/60 border-blue-500 ring-2 ring-blue-500/20 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-slate-300' }} flex items-center justify-between">
                            <div class="flex items-center gap-2 truncate">
                                <div class="rombel-dot w-2 h-2 rounded-full {{ $index === 0 ? 'bg-blue-600' : 'bg-slate-300 group-hover:bg-slate-400' }} transition-colors"></div>
                                <span class="text-xs sm:text-sm font-bold {{ $index === 0 ? 'text-blue-900' : 'text-slate-700 group-hover:text-slate-900' }} truncate">{{ $rombel }}</span>
                            </div>
                            <span class="rombel-check w-4 h-4 shrink-0 flex items-center justify-center">
                                @if($index === 0)
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                @endif
                            </span>
                        </button>
                    @empty
                        <button type="button" id="rombel-opt-default" onclick="selectRombel('PPLG X-3', 'rombel-opt-default')" class="rombel-item group p-2.5 rounded-xl border text-left transition-all cursor-pointer bg-gradient-to-br from-blue-50 to-indigo-50/60 border-blue-500 ring-2 ring-blue-500/20 shadow-xs flex items-center justify-between">
                            <div class="flex items-center gap-2 truncate">
                                <div class="rombel-dot w-2 h-2 rounded-full bg-blue-600"></div>
                                <span class="text-xs sm:text-sm font-bold text-blue-900">PPLG X-3</span>
                            </div>
                            <span class="rombel-check w-4 h-4 shrink-0 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                        </button>
                    @endforelse
                </div>
            </div>

            <!-- STEP 2: PILIH NAMA SISWA (SEARCH & CANDIDATES LIST) -->
            <div class="mt-4 sm:mt-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold tracking-wider text-slate-500 uppercase flex items-center gap-1.5">
                        <span class="w-4.5 h-4.5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center">2</span>
                        <span>Pilih Nama Siswa</span>
                    </span>
                    <span id="student-count-badge" class="text-[11px] text-slate-400 font-medium">
                        Ketik nama untuk filter
                    </span>
                </div>

                <!-- Modern Search Input Bar -->
                <div class="relative flex items-center">
                    <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" id="studentSearchInput" placeholder="Cari nama Anda atau nomor absen..." oninput="handleStudentFilter()" class="w-full bg-slate-50/80 hover:bg-slate-50 focus:bg-white text-xs sm:text-sm font-semibold text-slate-900 pl-9 pr-8 py-2 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:outline-none focus:ring-3 focus:ring-blue-500/10 transition-all placeholder:text-slate-400 placeholder:font-normal shadow-xs" />
                    <button type="button" id="clearSearchBtn" onclick="clearStudentSearch()" class="hidden absolute right-2.5 text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Student Candidates List Container -->
                <div id="studentListContainer" class="mt-2 border border-slate-200/80 rounded-2xl p-1.5 max-h-36 sm:max-h-40 overflow-y-auto space-y-1 bg-slate-50/40">
                    <!-- Populated dynamically via JS handleStudentFilter() -->
                </div>
            </div>

            <!-- LIVE SELECTED IDENTITY PREVIEW BOX -->
            <div id="modal-identity-preview" class="mt-3.5 sm:mt-4 p-3 bg-gradient-to-r from-blue-50/90 via-indigo-50/40 to-slate-50/60 rounded-2xl border border-blue-200/70 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shadow-xs shrink-0 tracking-wider">
                        <span id="modal-preview-avatar">AF</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Identitas yang Dipilih</p>
                        <p id="modal-preview-text" class="text-xs sm:text-sm font-bold text-slate-900 truncate">Ahmad Fauzi &bull; PPLG X-3 (Absen 02)</p>
                    </div>
                </div>
                <div class="shrink-0 pl-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-lg border border-emerald-200/80">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>Siap</span>
                    </span>
                </div>
            </div>

            <!-- Bottom Action Button: Masuk ke Dashboard Tugas -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-[11px] text-slate-400 text-center sm:text-left">
                    Identitas dapat diubah kapan saja di dashboard.
                </p>
                <button type="button" id="btn-modal-lanjut" onclick="confirmIdentityModal()" class="w-full sm:w-auto px-6 py-2.5 sm:py-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:via-indigo-500 hover:to-blue-600 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-blue-500/25 hover:shadow-blue-500/35 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer group">
                    <span>Masuk ke Dashboard Tugas</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL: LIHAT PENGUMPULAN (PREVIEW BERKAS YANG TELAH DIKUMPULKAN)          -->
    <!-- ========================================================================= -->
    <div id="modal-view-submission" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100">
            <button type="button" onclick="closeSubmissionModal()" class="absolute -top-3 -right-3 sm:-top-4 sm:-right-4 w-9 h-9 rounded-full bg-white shadow-md border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-slate-100 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Detail Pengumpulan Tugas</h3>
                    <p class="text-xs text-slate-500">Tugas telah tercatat di server pembelajaran</p>
                </div>
            </div>

            <div class="mt-6 space-y-3.5 bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-xs sm:text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Judul Tugas:</span>
                    <span id="preview-task-title" class="font-bold text-slate-800 text-right max-w-xs truncate">Tugas Mandiri</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Siswa:</span>
                    <span id="preview-student-name" class="font-semibold text-slate-800">Ahmad Fauzi (Absen 02)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Rombel:</span>
                    <span id="preview-student-rombel" class="font-semibold text-slate-800">PPLG X-3</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Kumpul:</span>
                    <span id="preview-timestamp" class="font-semibold text-slate-800">11 Okt 2024, 21:14 WIB</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Berkas Jawaban:</span>
                    <span id="preview-filename" class="font-semibold text-blue-600 truncate max-w-[180px]">Tugas_LHO_AhmadFauzi.pdf</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status Penilaian:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Review Guru</span>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeSubmissionModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer">
                    Tutup
                </button>
                <button type="button" onclick="alert('Mengunduh berkas jawaban: ' + document.getElementById('preview-filename').textContent)" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Unduh Berkas</span>
                </button>
            </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TOAST NOTIFICATION                                                        -->
    <!-- ========================================================================= -->
    <div id="toast-notification" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 border border-slate-700">
            <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <p id="toast-message" class="text-xs sm:text-sm font-medium">Tugas berhasil dikumpulkan!</p>
        </div>
    </div>


    <!-- Footer Component -->
    <x-footer />


    <!-- ========================================================================= -->
    <!-- INTERACTIVE JAVASCRIPT LOGIC                                              -->
    <!-- ========================================================================= -->
    <script>
        // Data dari Controller Laravel (Database & Mock Data)
        const serverStudents = @json($daftarSiswa ?? []);
        const serverRombels = @json($daftarRombel ?? []);

        // Demo data cadangan sesuai Figma
        const demoStudents = [
            { id: 101, nama: 'Ahmad Fauzi', absen: '02', rombel: 'PPLG X-3' },
            { id: 102, nama: 'Ahmad Rizky', absen: '03', rombel: 'PPLG X-3' },
            { id: 103, nama: 'Ahmad Yusuf', absen: '04', rombel: 'PPLG X-3' },
            { id: 104, nama: 'Siti Ahmad', absen: '28', rombel: 'PPLG X-3' },
            { id: 105, nama: 'Budi Santoso', absen: '05', rombel: 'PPLG X-3' },
            { id: 106, nama: 'Citra Dewi', absen: '06', rombel: 'DKV XI-3' },
            { id: 107, nama: 'Dimas Aditya', absen: '07', rombel: 'PPLG XI-5' },
            { id: 108, nama: 'Eka Ramadhan', absen: '08', rombel: 'TKJ XI-3' }
        ];

        // Format all students
        let allStudents = [];
        if (serverStudents && serverStudents.length > 0) {
            allStudents = serverStudents.map((s, idx) => ({
                id: s.id,
                nama: s.nama,
                absen: s.nis ? (s.nis.length >= 2 ? s.nis.slice(-2) : s.nis) : String(idx + 1).padStart(2, '0'),
                rombel: s.rombel || 'PPLG X-3'
            }));
            // Tambahkan demo students jika belum ada
            demoStudents.forEach(ds => {
                if (!allStudents.some(s => s.nama.toLowerCase() === ds.nama.toLowerCase() && s.rombel === ds.rombel)) {
                    allStudents.push(ds);
                }
            });
        } else {
            allStudents = demoStudents;
        }

        // Default initial rombel
        const defaultRombel = (serverRombels && serverRombels.length > 0) ? serverRombels[0] : 'PPLG X-3';

        // Check storage for previously selected student identity
        const storedRombel = localStorage.getItem('cbt_siswa_rombel') || sessionStorage.getItem('cbt_siswa_rombel');
        const storedStudent = localStorage.getItem('cbt_siswa_nama') || sessionStorage.getItem('cbt_siswa_nama');
        const storedAbsen = localStorage.getItem('cbt_siswa_absen') || sessionStorage.getItem('cbt_siswa_absen');
        const storedId = localStorage.getItem('cbt_siswa_id') || sessionStorage.getItem('cbt_siswa_id');

        let state = {
            selectedRombel: storedRombel || defaultRombel,
            selectedStudent: storedStudent || '',
            selectedAbsen: storedAbsen || '',
            selectedStudentId: storedId || null,
            currentTaskId: null,
            taskFiles: {
                1: null,
                2: null
            },
            hasSelectedIdentity: !!(storedRombel && storedStudent)
        };

        // Buka Pop Up Modal Identitas Otomatis Saat Pertama Kali Masuk
        window.addEventListener('DOMContentLoaded', () => {
            // Jika ada rombel terpilih, aktifkan rombel tersebut di list
            highlightSelectedRombelInList(state.selectedRombel);
            handleStudentFilter();

            // Sesuai permintaan pengguna:
            // "sebelum ke dasbaord tugas sebelum masuk pastikan yang pertama kali muncul pop up untuk memilih nama rombel yaa"
            // Jika siswa belum memilih identitas atau pertama kali masuk sesi ini:
            if (!state.hasSelectedIdentity) {
                // Pre-pilih siswa pertama di rombel untuk kenyamanan pengguna
                const initialCandidate = allStudents.find(s => s.rombel === state.selectedRombel) || allStudents[0];
                if (initialCandidate && !state.selectedStudent) {
                    state.selectedStudent = initialCandidate.nama;
                    state.selectedAbsen = initialCandidate.absen;
                    state.selectedStudentId = initialCandidate.id;
                }
                openIdentityModal();
            } else {
                updateIdentityDisplay();
            }
        });

        // Update tampilan identitas di banner atas dan kartu-kartu
        function updateIdentityDisplay() {
            const studentName = state.selectedStudent || 'Ahmad Fauzi';
            const studentAbsen = state.selectedAbsen || '02';
            const studentRombel = state.selectedRombel || 'PPLG X-3';
            const fullTitle = `${studentName} (Absen ${studentAbsen})`;

            // Initials untuk avatar
            const initials = studentName.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase() || 'SW';

            const bannerInitial = document.getElementById('banner-student-initial');
            const bannerName = document.getElementById('banner-student-name');
            const bannerRombel = document.getElementById('banner-student-rombel');
            const bannerAbsen = document.getElementById('banner-student-absen');

            if (bannerInitial) bannerInitial.textContent = initials;
            if (bannerName) bannerName.textContent = studentName;
            if (bannerRombel) bannerRombel.textContent = studentRombel;
            if (bannerAbsen) bannerAbsen.textContent = `Absen ${studentAbsen}`;

            // Update kartu tugas 1 & 2
            ['card-1', 'card-2'].forEach(prefix => {
                const nameEl = document.getElementById(`${prefix}-student-name`);
                const rombelEl = document.getElementById(`${prefix}-student-rombel`);
                if (nameEl) nameEl.textContent = fullTitle;
                if (rombelEl) rombelEl.textContent = studentRombel;
            });

            // Update indikator rombel di modal
            const rombelIndicator = document.getElementById('selected-rombel-indicator');
            if (rombelIndicator) {
                rombelIndicator.textContent = `${studentRombel} Terpilih`;
            }
        }

        // Mulai pengumpulan tugas
        function handleStartSubmission(taskId) {
            state.currentTaskId = taskId;
            // Jika identitas sudah dipilih, langsung buka dropzone upload
            if (state.hasSelectedIdentity && state.selectedStudent && state.selectedRombel) {
                showUploadSection(taskId);
            } else {
                openIdentityModal(taskId);
            }
        }

        // Buka Modal Identitas
        function openIdentityModal(taskId) {
            if (taskId) state.currentTaskId = taskId;
            const modal = document.getElementById('modal-identity');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            highlightSelectedRombelInList(state.selectedRombel);
            handleStudentFilter();
            updateModalPreview();
            updateModalLanjutButton();
        }

        // Tutup Modal Identitas
        function closeIdentityModal() {
            document.getElementById('modal-identity').classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Jika belum konfirmasi, tetap update banner dengan state default
            if (!state.hasSelectedIdentity) {
                updateIdentityDisplay();
                showToast('Kamu dapat mengganti Rombel & Nama kapan saja lewat tombol di atas.');
            }
        }

        // Update live preview identitas di dalam modal
        function updateModalPreview() {
            const studentName = state.selectedStudent || 'Pilih Siswa';
            const studentAbsen = state.selectedAbsen ? `(Absen ${state.selectedAbsen})` : '';
            const studentRombel = state.selectedRombel || '-';
            const initials = state.selectedStudent
                ? state.selectedStudent.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase()
                : '??';

            const avatarEl = document.getElementById('modal-preview-avatar');
            const textEl = document.getElementById('modal-preview-text');

            if (avatarEl) avatarEl.textContent = initials;
            if (textEl) {
                textEl.textContent = state.selectedStudent
                    ? `${studentName} • ${studentRombel} ${studentAbsen}`
                    : `Silakan pilih nama siswa untuk rombel ${studentRombel}`;
            }
        }

        // Highlight rombel terpilih di list
        function highlightSelectedRombelInList(rombelName) {
            document.querySelectorAll('.rombel-item').forEach(el => {
                const text = el.querySelector('span.text-xs, span.text-sm')?.innerText?.trim() || '';
                const checkContainer = el.querySelector('.rombel-check');
                const dot = el.querySelector('.rombel-dot');
                const textSpan = el.querySelector('span.text-xs, span.text-sm');

                if (text === rombelName) {
                    el.className = 'rombel-item group p-3 rounded-2xl border text-left transition-all cursor-pointer bg-gradient-to-br from-blue-50 to-indigo-50/60 border-blue-500 ring-2 ring-blue-500/20 shadow-xs flex items-center justify-between';
                    if (dot) dot.className = 'rombel-dot w-2 h-2 rounded-full bg-blue-600 transition-colors';
                    if (textSpan) textSpan.className = 'text-xs sm:text-sm font-bold text-blue-900 truncate';
                    if (checkContainer) {
                        checkContainer.innerHTML = `
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        `;
                    }
                } else {
                    el.className = 'rombel-item group p-3 rounded-2xl border text-left transition-all cursor-pointer bg-slate-50/70 hover:bg-white border-slate-200 hover:border-slate-300 flex items-center justify-between';
                    if (dot) dot.className = 'rombel-dot w-2 h-2 rounded-full bg-slate-300 group-hover:bg-slate-400 transition-colors';
                    if (textSpan) textSpan.className = 'text-xs sm:text-sm font-bold text-slate-700 group-hover:text-slate-900 truncate';
                    if (checkContainer) {
                        checkContainer.innerHTML = '';
                    }
                }
            });

            const rombelIndicator = document.getElementById('selected-rombel-indicator');
            if (rombelIndicator) {
                rombelIndicator.textContent = `${rombelName}`;
            }
            updateModalPreview();
        }

        // Pilih Rombel di Modal
        function selectRombel(rombelName, elementId) {
            state.selectedRombel = rombelName;
            highlightSelectedRombelInList(rombelName);

            // Cari siswa pertama di rombel ini dan set sebagai default
            const studentsInRombel = allStudents.filter(s => s.rombel === rombelName);
            if (studentsInRombel.length > 0) {
                state.selectedStudent = studentsInRombel[0].nama;
                state.selectedAbsen = studentsInRombel[0].absen;
                state.selectedStudentId = studentsInRombel[0].id;
            } else {
                state.selectedStudent = '';
                state.selectedAbsen = '';
                state.selectedStudentId = null;
            }

            // Reset input search
            const searchInput = document.getElementById('studentSearchInput');
            if (searchInput) searchInput.value = '';
            const clearBtn = document.getElementById('clearSearchBtn');
            if (clearBtn) clearBtn.classList.add('hidden');

            handleStudentFilter();
            updateModalPreview();
            updateModalLanjutButton();
        }

        // Bersihkan input pencarian
        function clearStudentSearch() {
            const searchInput = document.getElementById('studentSearchInput');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            const clearBtn = document.getElementById('clearSearchBtn');
            if (clearBtn) clearBtn.classList.add('hidden');
            handleStudentFilter();
        }

        // Filter Siswa berdasarkan input pencarian & rombel terpilih
        function handleStudentFilter() {
            const searchInput = document.getElementById('studentSearchInput');
            const query = (searchInput?.value || '').toLowerCase().trim();
            const clearBtn = document.getElementById('clearSearchBtn');
            const container = document.getElementById('studentListContainer');
            const countBadge = document.getElementById('student-count-badge');
            
            if (clearBtn) {
                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            if (!container) return;
            
            // Filter siswa berdasarkan rombel aktif dan query search
            let filtered = allStudents.filter(s => {
                const matchRombel = !state.selectedRombel || s.rombel === state.selectedRombel;
                const matchQuery = !query || s.nama.toLowerCase().includes(query) || s.absen.includes(query);
                return matchRombel && matchQuery;
            });

            if (countBadge) {
                countBadge.textContent = query 
                    ? `${filtered.length} siswa ditemukan`
                    : `${filtered.length} siswa di rombel ini`;
            }

            // Jika tidak ada siswa di rombel ini
            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="py-8 px-4 text-center">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-500 mx-auto flex items-center justify-center mb-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Tidak ada siswa ditemukan</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Coba kata kunci pencarian lain atau pilih rombel yang sesuai.</p>
                    </div>
                `;
                updateModalLanjutButton();
                return;
            }

            const colors = [
                'from-blue-600 to-indigo-600',
                'from-indigo-600 to-violet-600',
                'from-emerald-600 to-teal-600',
                'from-cyan-600 to-blue-600',
                'from-purple-600 to-fuchsia-600'
            ];

            let html = '';
            filtered.forEach((s, idx) => {
                const isSelected = (s.nama === state.selectedStudent);
                const initials = s.nama.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
                const color = colors[Math.abs(s.nama.charCodeAt(0) + idx) % colors.length];

                // Highlight query matching
                let displayName = s.nama;
                if (query) {
                    const regex = new RegExp(`(${query})`, 'gi');
                    displayName = s.nama.replace(regex, '<span class="font-black text-blue-600 bg-blue-100/80 px-1 rounded">$1</span>');
                }

                html += `
                    <div onclick="selectStudent('${s.nama}', '${s.absen}', ${s.id || 'null'}, this)" class="student-item p-2.5 rounded-2xl border transition-all cursor-pointer flex items-center justify-between gap-3 ${isSelected ? 'bg-white border-blue-500 ring-2 ring-blue-500/15 shadow-xs' : 'bg-white/80 hover:bg-white border-slate-200/80 hover:border-slate-300'}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr ${color} text-white font-extrabold text-[11px] flex items-center justify-center shrink-0 shadow-xs">
                                ${initials}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-bold ${isSelected ? 'text-blue-900' : 'text-slate-800'} truncate">
                                    ${displayName}
                                </p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.2 rounded">Absen ${s.absen}</span>
                                    ${s.rombel ? `<span class="text-[10px] text-slate-400 font-medium">${s.rombel}</span>` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0 pr-1">
                            <div class="w-5 h-5 rounded-full ${isSelected ? 'bg-blue-600 text-white shadow-xs' : 'border border-slate-300 text-transparent'} flex items-center justify-center transition-all">
                                <svg class="w-3 h-3 ${isSelected ? 'opacity-100' : 'opacity-0'}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            updateModalLanjutButton();
        }

        // Pilih Siswa di List
        function selectStudent(nama, absen, studentId, element) {
            state.selectedStudent = nama;
            state.selectedAbsen = absen;
            if (studentId) state.selectedStudentId = studentId;

            document.querySelectorAll('.student-item').forEach(el => {
                el.className = 'student-item p-2.5 rounded-2xl border transition-all cursor-pointer flex items-center justify-between gap-3 bg-white/80 hover:bg-white border-slate-200/80 hover:border-slate-300';
                const check = el.querySelector('.w-5.h-5');
                if (check) {
                    check.className = 'w-5 h-5 rounded-full border border-slate-300 text-transparent flex items-center justify-center transition-all';
                    check.querySelector('svg')?.classList.add('opacity-0');
                    check.querySelector('svg')?.classList.remove('opacity-100');
                }
            });

            if (element) {
                element.className = 'student-item p-2.5 rounded-2xl border transition-all cursor-pointer flex items-center justify-between gap-3 bg-white border-blue-500 ring-2 ring-blue-500/15 shadow-xs';
                const check = element.querySelector('.w-5.h-5');
                if (check) {
                    check.className = 'w-5 h-5 rounded-full bg-blue-600 text-white shadow-xs flex items-center justify-center transition-all';
                    check.querySelector('svg')?.classList.remove('opacity-0');
                    check.querySelector('svg')?.classList.add('opacity-100');
                }
            }

            updateModalPreview();
            updateModalLanjutButton();
        }

        // Update tombol lanjut di modal
        function updateModalLanjutButton() {
            const btn = document.getElementById('btn-modal-lanjut');
            if (!btn) return;

            if (state.selectedRombel && state.selectedStudent) {
                btn.className = 'w-full sm:w-auto px-7 py-3.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:via-indigo-500 hover:to-blue-600 text-white text-sm font-bold rounded-2xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/35 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer group';
                btn.disabled = false;
            } else {
                btn.className = 'w-full sm:w-auto px-7 py-3.5 bg-slate-100 text-slate-400 text-sm font-semibold rounded-2xl cursor-not-allowed flex items-center justify-center gap-2';
                btn.disabled = true;
            }
        }

        // Konfirmasi identitas dari modal & masuk ke dashboard tugas
        function confirmIdentityModal() {
            if (!state.selectedRombel || !state.selectedStudent) return;

            state.hasSelectedIdentity = true;
            localStorage.setItem('cbt_siswa_rombel', state.selectedRombel);
            localStorage.setItem('cbt_siswa_nama', state.selectedStudent);
            localStorage.setItem('cbt_siswa_absen', state.selectedAbsen);
            if (state.selectedStudentId) {
                localStorage.setItem('cbt_siswa_id', state.selectedStudentId);
            }

            closeIdentityModal();
            updateIdentityDisplay();

            if (state.currentTaskId) {
                showUploadSection(state.currentTaskId);
            } else {
                showToast(`Selamat datang, ${state.selectedStudent} (${state.selectedRombel})!`);
            }
        }

        // Tampilkan Bagian Unggah Jawaban di Kartu Tertentu
        function showUploadSection(taskId) {
            const card = document.getElementById(`card-tugas-${taskId}`);
            const initialAction = document.getElementById(`card-${taskId}-initial-action`);
            const uploadSection = document.getElementById(`card-${taskId}-upload-section`);

            if (card) {
                // Beri border aktif 2px warna biru (sesuai 'Latihan & Tugas — Unggah Jawaban.png')
                card.classList.add('border-2', 'border-blue-600', 'shadow-md');
                card.classList.remove('border-slate-200/90');
            }

            if (initialAction) initialAction.classList.add('hidden');
            if (uploadSection) uploadSection.classList.remove('hidden');

            // Reset error jika ada
            clearErrorState(taskId);
        }

        // Batalkan Upload dan Kembalikan ke Tampilan Awal
        function cancelUpload(taskId) {
            const card = document.getElementById(`card-tugas-${taskId}`);
            const initialAction = document.getElementById(`card-${taskId}-initial-action`);
            const uploadSection = document.getElementById(`card-${taskId}-upload-section`);

            if (card) {
                card.classList.remove('border-2', 'border-blue-600', 'shadow-md');
                card.classList.add('border-slate-200/90');
            }

            if (initialAction) initialAction.classList.remove('hidden');
            if (uploadSection) uploadSection.classList.add('hidden');

            resetFileUpload(taskId);
        }

        // Trigger input file
        function triggerFileInput(taskId) {
            const input = document.getElementById(`file-input-${taskId}`);
            if (input) input.click();
        }

        // Handle file yang dipilih
        function handleFileSelected(taskId) {
            const input = document.getElementById(`file-input-${taskId}`);
            if (input && input.files && input.files[0]) {
                const file = input.files[0];
                state.taskFiles[taskId] = file;
                renderFilePreview(taskId, file);
                clearErrorState(taskId);
            }
        }

        // Render preview file terpilih
        function renderFilePreview(taskId, file) {
            const dropzoneContent = document.getElementById(`card-${taskId}-dropzone-content`);
            const filePreview = document.getElementById(`card-${taskId}-file-preview`);
            const fileNameEl = document.getElementById(`card-${taskId}-file-name`);
            const fileSizeEl = document.getElementById(`card-${taskId}-file-size`);
            const dropzone = document.getElementById(`card-${taskId}-dropzone`);

            if (dropzoneContent) dropzoneContent.classList.add('hidden');
            if (filePreview) filePreview.classList.remove('hidden');
            if (fileNameEl) fileNameEl.textContent = file.name;
            if (fileSizeEl) fileSizeEl.textContent = formatBytes(file.size);

            if (dropzone) {
                dropzone.className = 'mt-4 border-2 border-emerald-400 bg-emerald-50/20 rounded-2xl py-8 px-6 flex flex-col items-center justify-center text-center cursor-pointer transition select-none';
            }

            // Aktifkan tombol submit
            const submitBtn = document.getElementById(`card-${taskId}-submit-btn`);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.className = taskId === 1
                    ? 'px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer'
                    : 'px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer';
            }
        }

        // Reset file upload
        function resetFileUpload(taskId) {
            state.taskFiles[taskId] = null;
            const input = document.getElementById(`file-input-${taskId}`);
            if (input) input.value = '';

            const dropzoneContent = document.getElementById(`card-${taskId}-dropzone-content`);
            const filePreview = document.getElementById(`card-${taskId}-file-preview`);
            const dropzone = document.getElementById(`card-${taskId}-dropzone`);

            if (dropzoneContent) dropzoneContent.classList.remove('hidden');
            if (filePreview) filePreview.classList.add('hidden');

            if (dropzone) {
                dropzone.className = taskId === 1
                    ? 'mt-4 border-2 border-dashed border-blue-400 bg-blue-50/15 rounded-2xl py-12 px-6 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-blue-50/30 transition group select-none relative'
                    : 'mt-4 border-2 border-dashed border-rose-300 bg-rose-50/15 rounded-2xl py-12 px-6 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-rose-50/30 transition group select-none relative';
            }

            clearErrorState(taskId);
        }

        // Drag and Drop Handlers
        function handleDragOver(e, taskId) {
            e.preventDefault();
            const dropzone = document.getElementById(`card-${taskId}-dropzone`);
            if (dropzone && !state.taskFiles[taskId]) {
                dropzone.classList.add('bg-blue-100/50', 'border-blue-600');
            }
        }

        function handleDragLeave(e, taskId) {
            e.preventDefault();
            const dropzone = document.getElementById(`card-${taskId}-dropzone`);
            if (dropzone && !state.taskFiles[taskId]) {
                dropzone.classList.remove('bg-blue-100/50', 'border-blue-600');
            }
        }

        function handleDrop(e, taskId) {
            e.preventDefault();
            const dropzone = document.getElementById(`card-${taskId}-dropzone`);
            if (dropzone && !state.taskFiles[taskId]) {
                dropzone.classList.remove('bg-blue-100/50', 'border-blue-600');
            }

            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
                const file = e.dataTransfer.files[0];
                state.taskFiles[taskId] = file;
                renderFilePreview(taskId, file);
                clearErrorState(taskId);
            }
        }

        // Tampilkan State Error (Sesuai Gambar 'Latihan & Tugas — Error Berkas Wajib.png')
        function triggerErrorState(taskId) {
            const dropzone = document.getElementById(`card-${taskId}-dropzone`);
            const dropzoneIcon = document.getElementById(`card-${taskId}-dropzone-icon`);
            const hintText = document.getElementById(`card-${taskId}-hint-text`);
            const errorText = document.getElementById(`card-${taskId}-error-text`);
            const submitBtn = document.getElementById(`card-${taskId}-submit-btn`);

            // Ubah background dropzone jadi merah muda lembut
            if (dropzone) {
                dropzone.className = 'mt-4 border-2 border-dashed border-rose-400 bg-rose-50/70 rounded-2xl py-12 px-6 flex flex-col items-center justify-center text-center cursor-pointer transition select-none relative';
            }

            // Ubah ikon awan menjadi ikon segitiga seru merah
            if (dropzoneIcon) {
                dropzoneIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center text-rose-500 mb-2';
                dropzoneIcon.innerHTML = `
                    <svg class="w-8 h-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                `;
            }

            // Tampilkan error text
            if (hintText) hintText.classList.add('hidden');
            if (errorText) errorText.classList.remove('hidden');

            // Disabled submit button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.className = 'px-6 py-2.5 bg-slate-300 text-slate-500 text-sm font-semibold rounded-xl cursor-not-allowed transition';
            }
        }

        // Bersihkan state error
        function clearErrorState(taskId) {
            const dropzoneIcon = document.getElementById(`card-${taskId}-dropzone-icon`);
            const hintText = document.getElementById(`card-${taskId}-hint-text`);
            const errorText = document.getElementById(`card-${taskId}-error-text`);
            const submitBtn = document.getElementById(`card-${taskId}-submit-btn`);

            if (dropzoneIcon) {
                dropzoneIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center text-blue-500 mb-2';
                dropzoneIcon.innerHTML = `
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                `;
            }

            if (hintText) hintText.classList.remove('hidden');
            if (errorText) errorText.classList.add('hidden');

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.className = taskId === 1
                    ? 'px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer'
                    : 'px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer';
            }
        }

        // Submit tugas
        function submitTugas(taskId) {
            // Validasi apakah berkas sudah diunggah
            if (!state.taskFiles[taskId]) {
                triggerErrorState(taskId);
                return;
            }

            // Simulasi pengiriman data
            const file = state.taskFiles[taskId];
            const btn = document.getElementById(`card-${taskId}-submit-btn`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="inline-block animate-spin mr-2">⟳</span> Mengirim...';
            }

            setTimeout(() => {
                // Sukses terkirim! Ubah tampilan card menjadi "Sudah Dikumpulkan"
                const card = document.getElementById(`card-tugas-${taskId}`);
                const uploadSection = document.getElementById(`card-${taskId}-upload-section`);
                const submittedAction = document.getElementById(`card-${taskId}-submitted-action`);

                if (card) {
                    card.classList.remove('border-2', 'border-blue-600', 'shadow-md');
                    card.classList.add('border-slate-200/90');
                }

                if (uploadSection) uploadSection.classList.add('hidden');
                if (submittedAction) submittedAction.classList.remove('hidden');

                // Tampilkan Toast
                showToast(`Tugas berhasil dikumpulkan atas nama ${state.selectedStudent}!`);
            }, 600);
        }

        // Tampilkan Modal Detail Pengumpulan
        function viewSubmission(taskId, taskTitle) {
            document.getElementById('preview-task-title').textContent = taskTitle;
            document.getElementById('preview-student-name').textContent = `${state.selectedStudent} (Absen ${state.selectedAbsen})`;
            document.getElementById('preview-student-rombel').textContent = state.selectedRombel;
            
            const file = state.taskFiles[taskId];
            if (file) {
                document.getElementById('preview-filename').textContent = file.name;
                document.getElementById('preview-timestamp').textContent = 'Hari ini, ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            } else {
                document.getElementById('preview-filename').textContent = 'Tugas_LHO_' + state.selectedStudent.replace(/\s+/g, '') + '.pdf';
                document.getElementById('preview-timestamp').textContent = '12 Okt 2024, 21:14 WIB';
            }

            document.getElementById('modal-view-submission').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSubmissionModal() {
            document.getElementById('modal-view-submission').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Toast feedback
        function showToast(message) {
            const toast = document.getElementById('toast-notification');
            const toastMsg = document.getElementById('toast-message');
            if (toast && toastMsg) {
                toastMsg.textContent = message;
                toast.classList.remove('translate-y-20', 'opacity-0');
                setTimeout(() => {
                    toast.classList.add('translate-y-20', 'opacity-0');
                }, 4000);
            }
        }

        function formatBytes(bytes, decimals = 1) {
            if (!+bytes) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
        }
    </script>
</body>
</html>
