<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bahasa Indonesia - Kelas X SMA | Portal Pembelajaran CBT PPLG</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite & CDN Fallback for instant high-fidelity rendering) -->
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
            background-color: #ffffff;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-white text-slate-800 antialiased selection:bg-blue-100 selection:text-blue-700">

    <!-- Top Navigation Bar -->
    <x-navbar />

    <!-- Main Content Container -->
    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-10 pb-16">

        <!-- Hero Section -->
        <section class="flex flex-col md:flex-row md:items-center justify-between gap-8 pt-2 pb-6">
            <!-- Left Column: Title & Descriptions -->
            <div class="max-w-2xl">
                <!-- Badge -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/70 text-amber-700 text-xs font-semibold mb-4 shadow-xs">
                    <svg class="w-3.5 h-3.5 text-amber-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l2.4 7.4h7.6l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4-6.2-4.5h7.6z" />
                    </svg>
                    <span>Kunjungan Pertama Siswa</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-3xl sm:text-4xl lg:text-[42px] font-extrabold text-slate-900 tracking-tight leading-tight">
                    Materi Pembelajaran
                </h1>

                <!-- Subtitle -->
                <p class="mt-3 text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl">
                    Akses semua bahan ajar tanpa perlu login. Silakan telusuri materi dan langsung mulai belajar mandiri secara instan.
                </p>
            </div>

            <!-- Right Column: Visual Floating Illustration Graphic -->
            <div class="relative flex items-center justify-center md:justify-end w-full md:w-80 h-44 shrink-0 select-none">
                <!-- Background Soft Pastel Glows -->
                <div class="absolute w-44 h-44 rounded-full bg-blue-100/70 -top-2 right-6 blur-2xl pointer-events-none"></div>
                <div class="absolute w-36 h-36 rounded-full bg-emerald-100/70 bottom-0 right-16 blur-xl pointer-events-none"></div>

                <!-- Floating Rounded Graphic Container -->
                <div class="relative flex items-center">
                    <!-- Main Card: Blue Book Icon -->
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-white/95 backdrop-blur-xs border border-slate-100 shadow-[0_12px_30px_rgba(37,99,235,0.08)] flex items-center justify-center -rotate-6 hover:rotate-0 transition-transform duration-300">
                        <div class="w-12 h-12 text-blue-500">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" class="w-full h-full">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                    </div>

                    <!-- Offset Card: Emerald Pen Icon -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/95 backdrop-blur-xs border border-slate-100 shadow-[0_10px_25px_rgba(16,185,129,0.12)] flex items-center justify-center rotate-12 -ml-6 -mt-8 hover:rotate-6 transition-transform duration-300 z-10">
                        <div class="w-8 h-8 text-emerald-500">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" class="w-full h-full">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Divider Line -->
        <hr class="border-t border-slate-200/80 my-8 sm:my-10" />

        <!-- Chapter Sections -->
        <div class="space-y-12 sm:space-y-14">
            @foreach ($daftarBab as $bab)
                <section class="scroll-mt-24">
                    <!-- Chapter Header -->
                    <div class="mb-5">
                        <span class="text-xs font-bold text-blue-600 tracking-wider uppercase block">
                            {{ $bab['kode'] ?? ('BAB ' . ($loop->iteration)) }}
                        </span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">
                            {{ $bab['judul'] ?? ($bab->nama_bab ?? 'Bab Pembelajaran') }}
                        </h2>
                        @if (!empty($bab['deskripsi']))
                            <p class="text-sm text-slate-500 mt-1 max-w-3xl leading-relaxed">
                                {{ $bab['deskripsi'] }}
                            </p>
                        @endif
                    </div>

                    <!-- Materials Card Grid (3 Columns on Desktop) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                        @php
                            $materiList = $bab['materi'] ?? ($bab->materi ?? []);
                        @endphp

                        @foreach ($materiList as $item)
                            @php
                                $tipe = strtoupper($item['tipe'] ?? ($item->jenis_file ?? 'PDF'));
                                $judul = $item['judul'] ?? 'Materi Pembelajaran';
                                $tanggal = $item['tanggal'] ?? 'Terbaru';
                                $estimasi = $item['estimasi'] ?? '15 mnt';
                                $aksi = strtolower($item['aksi'] ?? ($tipe === 'VIDEO' ? 'buka' : 'unduh'));
                                $url = $item['url'] ?? ($item->url_eksternal ?? '#');
                            @endphp

                            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between hover:shadow-md hover:border-slate-300/80 transition-all duration-200 group">
                                <div>
                                    <!-- Top Row: Badge & Date -->
                                    <div class="flex items-center justify-between gap-2 mb-3.5">
                                        @if ($tipe === 'VIDEO')
                                            <!-- Video Badge -->
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide bg-blue-50 text-blue-600 border border-blue-100/60">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                                VIDEO
                                            </span>
                                        @elseif ($tipe === 'DOKUMEN')
                                            <!-- Dokumen Badge -->
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide bg-emerald-50 text-emerald-600 border border-emerald-100/60">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                DOKUMEN
                                            </span>
                                        @else
                                            <!-- PDF Badge -->
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide bg-rose-50 text-rose-600 border border-rose-100/60">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                PDF
                                            </span>
                                        @endif

                                        <span class="text-xs text-slate-400 font-medium">
                                            {{ $tanggal }}
                                        </span>
                                    </div>

                                    <!-- Material Title -->
                                    <h3 class="font-bold text-slate-900 text-sm sm:text-[15px] leading-snug group-hover:text-blue-600 transition-colors duration-150 line-clamp-2">
                                        {{ $judul }}
                                    </h3>
                                </div>

                                <!-- Card Footer: Duration & Action Button -->
                                <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                                    <!-- Estimated Duration -->
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                                        </svg>
                                        <span>Estimasi: {{ $estimasi }}</span>
                                    </div>

                                    <!-- Action Button -->
                                    @if ($aksi === 'buka')
                                        <button type="button" onclick="handleOpenMaterial('{{ addslashes($judul) }}', '{{ $tipe }}')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-600 hover:bg-emerald-100/90 active:scale-95 transition-all shadow-2xs">
                                            <span>Buka</span>
                                            <svg class="w-3.5 h-3.5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button" onclick="handleDownloadMaterial('{{ addslashes($judul) }}', '{{ $tipe }}')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100/90 active:scale-95 transition-all shadow-2xs">
                                            <span>Unduh</span>
                                            <svg class="w-3.5 h-3.5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </main>

    <!-- Footer Component -->
    <x-footer />

    <!-- Interactive Material Modal / Notification Toast -->
    <div id="actionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs hidden transition-opacity">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center transform transition-all">
            <div id="modalIconContainer" class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center">
                <!-- Injected via JavaScript -->
            </div>
            <h3 id="modalTitle" class="text-lg font-bold text-slate-900 mb-1">Judul Materi</h3>
            <p id="modalDesc" class="text-sm text-slate-600 mb-6 leading-relaxed">Materi sedang disiapkan untuk pembelajaran mandiri.</p>
            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="closeModal()" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 transition">
                    Tutup
                </button>
                <button type="button" id="modalConfirmBtn" onclick="closeModal()" class="w-full px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition shadow-sm">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <!-- Interactive Script -->
    <script>
        function handleDownloadMaterial(title, type) {
            const modal = document.getElementById('actionModal');
            const iconContainer = document.getElementById('modalIconContainer');
            const titleEl = document.getElementById('modalTitle');
            const descEl = document.getElementById('modalDesc');
            const confirmBtn = document.getElementById('modalConfirmBtn');

            iconContainer.className = 'w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-blue-50 text-blue-600';
            iconContainer.innerHTML = `
                <svg class="w-7 h-7 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            `;
            titleEl.textContent = 'Unduh ' + type;
            descEl.textContent = 'Mempersiapkan dokumen "' + title + '" untuk diunduh ke perangkat Anda.';
            confirmBtn.textContent = 'Unduh Sekarang';
            confirmBtn.className = 'w-full px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition shadow-sm';

            modal.classList.remove('hidden');
        }

        function handleOpenMaterial(title, type) {
            const modal = document.getElementById('actionModal');
            const iconContainer = document.getElementById('modalIconContainer');
            const titleEl = document.getElementById('modalTitle');
            const descEl = document.getElementById('modalDesc');
            const confirmBtn = document.getElementById('modalConfirmBtn');

            iconContainer.className = 'w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-emerald-50 text-emerald-600';
            iconContainer.innerHTML = `
                <svg class="w-7 h-7 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `;
            titleEl.textContent = 'Buka Video Pembelajaran';
            descEl.textContent = 'Menyiapkan pemutar video pembelajaran interaktif untuk "' + title + '".';
            confirmBtn.textContent = 'Tonton Sekarang';
            confirmBtn.className = 'w-full px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition shadow-sm';

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('actionModal').classList.add('hidden');
        }

        // Close modal on Escape key or outside click
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
        document.getElementById('actionModal').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) closeModal();
        });
    </script>
</body>
</html>
