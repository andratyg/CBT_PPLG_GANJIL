<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Presensi Siswa - GuruPortal LMS Indonesia</title>

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

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F8FAFC;
        }
    </style>
</head>
<body class="min-h-screen bg-[#F8FAFC] text-slate-800 antialiased flex selection:bg-blue-100 selection:text-blue-700">

    <!-- LEFT SIDEBAR COMPONENT -->
    <x-sidebar active="presensi" />

    <!-- MAIN CONTENT AREA -->
    <main class="ml-64 flex-1 min-h-screen bg-[#F8FAFC] p-6 lg:p-10 flex flex-col justify-between">
        <div class="max-w-7xl w-full mx-auto space-y-6">

            <!-- Top Header & Teacher Info -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-[26px] font-bold text-slate-900 tracking-tight">
                        Kelola Presensi Siswa
                    </h2>
                    <p class="text-xs sm:text-[13px] text-slate-500 font-medium mt-0.5 flex items-center gap-2">
                        <span>Rombel Aktif: <strong class="text-slate-800 font-semibold">{{ $activeRombel }}</strong> ({{ count($daftarSiswa) }} Siswa Terdaftar)</span>
                    </p>
                </div>

                <!-- Teacher Profile -->
                <div class="flex items-center gap-3.5 self-start sm:self-auto">
                    <div class="text-right">
                        <div class="text-sm font-bold text-slate-800 leading-tight">
                            {{ $guruNama ?? 'Pak Andi Wijaya' }}
                        </div>
                        <div class="text-[11px] text-slate-400 font-medium mt-0.5">
                            NIP. {{ $guruNip ?? '198204122009031002' }}
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-[#3B82F6] text-white font-bold text-sm flex items-center justify-center shadow-xs">
                        AW
                    </div>
                </div>
            </div>

            <!-- Controls: Rombel Filter & Pertemuan Selector -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                    <!-- Rombel (Kelas) Selector & Context -->
                    <div>
                        <label for="rombelSelect" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                            Pilih Rombel (Kelas)
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="relative min-w-[140px]">
                                <select
                                    id="rombelSelect"
                                    onchange="switchRombel(this.value)"
                                    class="w-full text-sm font-semibold text-slate-800 bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-xl pl-3.5 pr-9 py-2 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer appearance-none shadow-2xs"
                                >
                                    @foreach ($daftarRombel as $r)
                                        <option value="{{ $r }}" {{ $activeRombel === $r ? 'selected' : '' }}>
                                            {{ $r }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            <div class="text-xs text-slate-500 font-medium hidden sm:flex items-center gap-2 border-l border-slate-200 pl-3 whitespace-nowrap">
                                <span class="font-semibold text-slate-700">Basis Data</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-slate-400">Ganjil 2026/2027</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pertemuan Selector & Action -->
                    <div>
                        <label for="pertemuanSelect" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                            Pilih Sesi Pertemuan
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="relative min-w-[240px] sm:min-w-[290px]">
                                <select
                                    id="pertemuanSelect"
                                    onchange="switchPertemuan(this.value)"
                                    class="w-full text-sm font-semibold text-slate-800 bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-xl pl-3.5 pr-9 py-2 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer appearance-none truncate shadow-2xs"
                                >
                                    @foreach ($daftarPertemuan as $p)
                                        <option value="{{ $p->id }}" {{ ($activePertemuan && $activePertemuan->id === $p->id) ? 'selected' : '' }}>
                                            Pertemuan {{ $p->pertemuan_ke }} &mdash; {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('l, d F Y') }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            <button
                                type="button"
                                onclick="openModal('modalTambahPertemuan')"
                                class="px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-xl transition cursor-pointer shadow-xs flex items-center gap-1.5 shrink-0"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <span>Tambah Pertemuan</span>
                            </button>
                        </div>
                    </div>

                </div>

                @if ($activePertemuan && !empty($activePertemuan->topik))
                    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-400 uppercase tracking-wider text-[11px]">Topik:</span>
                            <span class="font-medium text-slate-800">{{ $activePertemuan->topik }}</span>
                        </div>
                        <span class="text-slate-400 text-[11px]">Pertemuan Ke-{{ $activePertemuan->pertemuan_ke }}</span>
                    </div>
                @endif
            </div>

            <!-- Ringkasan Presensi Kelas -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-1">
                <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Ringkasan Kehadiran Kelas
                </h3>

                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span id="badgeHadir" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                        Hadir: <span id="statHadir">0% (0)</span>
                    </span>
                    <span id="badgeIzin" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/80">
                        Izin: <span id="statIzin">0% (0)</span>
                    </span>
                    <span id="badgeSakit" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/80">
                        Sakit: <span id="statSakit">0% (0)</span>
                    </span>
                    <span id="badgeAlpa" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/80">
                        Alpa: <span id="statAlpa">0% (0)</span>
                    </span>
                    <span id="badgeDispensasi" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200/80">
                        Dispensasi: <span id="statDispensasi">0% (0)</span>
                    </span>
                </div>
            </div>

            <!-- Tabel Presensi Siswa Dinamis Berbasis Database -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden mt-6">
                @if (empty($daftarSiswa))
                    <!-- Empty State jika rombel belum punya siswa -->
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-800">Belum Ada Siswa di Rombel {{ $activeRombel }}</h4>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mt-1 mb-5">
                            Rombel ini belum memiliki data siswa terdaftar. Silakan tambahkan dan kelola data siswa melalui menu Data Master.
                        </p>
                        <a
                            href="{{ route('admin.master', ['tab' => 'siswa', 'rombel' => $activeRombel]) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-xs transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                            <span>Kelola Siswa di Data Master</span>
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-xs font-semibold text-slate-700 bg-slate-50/50">
                                    <th class="w-16 px-6 py-4 text-slate-400 font-semibold">No</th>
                                    <th class="px-6 py-4">Nama Siswa</th>
                                    <th class="px-6 py-4 text-center">Status Presensi</th>
                                    <th class="px-6 py-4 text-right">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="siswaTableBody" class="divide-y divide-slate-100 text-sm">
                                @foreach ($daftarSiswa as $siswa)
                                    <tr class="hover:bg-slate-50/60 transition-colors" data-id="{{ $siswa['id'] }}" data-index="{{ $loop->index }}" data-status="{{ $siswa['status'] }}">
                                        <!-- No -->
                                        <td class="px-6 py-4 text-sm font-semibold text-slate-400">
                                            {{ $siswa['no'] }}
                                        </td>

                                        <!-- Nama Siswa -->
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800 text-sm">
                                                {{ $siswa['nama'] }}
                                            </div>
                                            <div class="text-xs text-slate-400 font-medium mt-0.5 flex items-center gap-2">
                                                <span>NIS. {{ $siswa['nis'] }}</span>
                                                @if(!empty($siswa['rayon']))
                                                    <span>&bull;</span>
                                                    <span>Rayon: {{ $siswa['rayon'] }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Status Presensi (Segmented Pill Buttons) -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Hadir -->
                                                <button
                                                    type="button"
                                                    onclick="setStatus({{ $loop->index }}, 'hadir')"
                                                    id="btn-hadir-{{ $loop->index }}"
                                                    class="status-btn px-4 py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $siswa['status'] === 'hadir' ? 'bg-[#ECFDF5] text-[#10B981] border border-[#6EE7B7]' : 'bg-[#F1F5F9] text-slate-400 hover:text-slate-600 border border-slate-200/60' }}"
                                                >
                                                    Hadir
                                                </button>

                                                <!-- Izin -->
                                                <button
                                                    type="button"
                                                    onclick="setStatus({{ $loop->index }}, 'izin')"
                                                    id="btn-izin-{{ $loop->index }}"
                                                    class="status-btn px-4 py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $siswa['status'] === 'izin' ? 'bg-[#EFF6FF] text-[#3B82F6] border border-[#93C5FD]' : 'bg-[#F1F5F9] text-slate-400 hover:text-slate-600 border border-slate-200/60' }}"
                                                >
                                                    Izin
                                                </button>

                                                <!-- Sakit -->
                                                <button
                                                    type="button"
                                                    onclick="setStatus({{ $loop->index }}, 'sakit')"
                                                    id="btn-sakit-{{ $loop->index }}"
                                                    class="status-btn px-4 py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $siswa['status'] === 'sakit' ? 'bg-[#FFFBEB] text-[#F59E0B] border border-[#FCD34D]' : 'bg-[#F1F5F9] text-slate-400 hover:text-slate-600 border border-slate-200/60' }}"
                                                >
                                                    Sakit
                                                </button>

                                                <!-- Alpa -->
                                                <button
                                                    type="button"
                                                    onclick="setStatus({{ $loop->index }}, 'alpa')"
                                                    id="btn-alpa-{{ $loop->index }}"
                                                    class="status-btn px-4 py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $siswa['status'] === 'alpa' ? 'bg-[#FEF2F2] text-[#EF4444] border border-[#FCA5A5]' : 'bg-[#F1F5F9] text-slate-400 hover:text-slate-600 border border-slate-200/60' }}"
                                                >
                                                    Alpa
                                                </button>

                                                <!-- Dispensasi -->
                                                <button
                                                    type="button"
                                                    onclick="setStatus({{ $loop->index }}, 'dispensasi')"
                                                    id="btn-dispensasi-{{ $loop->index }}"
                                                    class="status-btn px-4 py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $siswa['status'] === 'dispensasi' ? 'bg-[#F0FDFA] text-[#14B8A6] border border-[#5EEAD4]' : 'bg-[#F1F5F9] text-slate-400 hover:text-slate-600 border border-slate-200/60' }}"
                                                >
                                                    Dispensasi
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Keterangan -->
                                        <td class="px-6 py-4 text-right">
                                            <span id="ket-{{ $loop->index }}" class="text-xs font-medium {{ $siswa['keterangan'] === 'Tersimpan' ? 'text-slate-400' : 'text-amber-600 font-semibold' }}">
                                                {{ $siswa['keterangan'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Footer Action Row -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 pb-12">
                <div id="lastUpdatedText" class="text-xs text-slate-400 font-normal">
                    Database status: <span class="text-emerald-600 font-semibold">Tersambung (MySQL)</span> &bull; Terakhir disimpan hari ini
                </div>

                @if (!empty($daftarSiswa))
                    <button
                        type="button"
                        id="btnSimpanPresensi"
                        onclick="simpanPresensiKeDatabase()"
                        class="inline-flex items-center justify-center gap-2 px-7 py-3 bg-[#3B82F6] hover:bg-[#2563EB] active:scale-[0.99] text-white font-semibold text-sm rounded-xl shadow-xs transition duration-150 cursor-pointer"
                    >
                        <svg id="saveSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span id="saveBtnText">Simpan Presensi</span>
                    </button>
                @endif
            </div>
        </div>
    </main>



    <!-- MODAL 2: TAMBAH PERTEMUAN -->
    <div id="modalTambahPertemuan" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-7 transform transition-all">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Tambah Pertemuan Baru</h3>
                        <p class="text-xs text-slate-500">Buat sesi presensi &amp; pembelajaran baru</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modalTambahPertemuan')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ url('/admin/pertemuan/tambah') }}" method="POST" class="space-y-4 pt-5">
                @csrf
                <input type="hidden" name="rombel" value="{{ $activeRombel }}">

                <div>
                    <label for="pertemuanKe" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pertemuan Ke-
                    </label>
                    <input
                        type="number"
                        name="pertemuan_ke"
                        id="pertemuanKe"
                        required
                        min="1"
                        value="{{ ($daftarPertemuan->max('pertemuan_ke') ?? 0) + 1 }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
                    >
                </div>

                <div>
                    <label for="pertemuanTanggal" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Pelaksanaan
                    </label>
                    <input
                        type="date"
                        name="tanggal"
                        id="pertemuanTanggal"
                        required
                        value="{{ date('Y-m-d') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
                    >
                </div>

                <div>
                    <label for="pertemuanTopik" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Topik / Materi Pertemuan
                    </label>
                    <input
                        type="text"
                        name="topik"
                        id="pertemuanTopik"
                        required
                        placeholder="Contoh: Implementasi Query Join & Subquery SQL"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
                    >
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modalTambahPertemuan')" class="w-full py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-500 transition shadow-sm">
                        Buat Pertemuan
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- Toast Notification -->
    <div id="saveToast" class="fixed bottom-6 right-6 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-sm font-semibold transition-all duration-300 opacity-0 translate-y-4 pointer-events-none z-50">
        <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <span id="toastMessage">Data presensi berhasil disimpan ke database!</span>
    </div>

    <!-- Scripts for Multi-Rombel, Pertemuan & Database AJAX Persistence -->
    <script>
        const statusConfig = {
            hadir: {
                active: 'bg-[#ECFDF5] text-[#10B981] border border-[#6EE7B7]',
            },
            izin: {
                active: 'bg-[#EFF6FF] text-[#3B82F6] border border-[#93C5FD]',
            },
            sakit: {
                active: 'bg-[#FFFBEB] text-[#F59E0B] border border-[#FCD34D]',
            },
            alpa: {
                active: 'bg-[#FEF2F2] text-[#EF4444] border border-[#FCA5A5]',
            },
            dispensasi: {
                active: 'bg-[#F0FDFA] text-[#14B8A6] border border-[#5EEAD4]',
            }
        };

        const inactiveClasses = 'bg-[#F1F5F9] text-slate-400 hover:text-slate-600 border border-slate-200/60';

        function switchRombel(rombel) {
            const pertemuanId = document.getElementById('pertemuanSelect')?.value || '';
            window.location.href = `{{ url('/admin/presensi') }}?rombel=${encodeURIComponent(rombel)}&pertemuan_id=${pertemuanId}`;
        }

        function switchPertemuan(pertemuanId) {
            const rombel = document.getElementById('rombelSelect')?.value || '{{ $activeRombel }}';
            window.location.href = `{{ url('/admin/presensi') }}?rombel=${encodeURIComponent(rombel)}&pertemuan_id=${pertemuanId}`;
        }

        function setStatus(index, newStatus) {
            const row = document.querySelector(`tr[data-index="${index}"]`);
            if (!row) return;

            row.setAttribute('data-status', newStatus);

            // Update button styles
            const types = ['hadir', 'izin', 'sakit', 'alpa', 'dispensasi'];
            types.forEach(t => {
                const btn = document.getElementById(`btn-${t}-${index}`);
                if (btn) {
                    if (t === newStatus) {
                        btn.className = `status-btn px-4 py-1.5 rounded-full text-xs font-bold transition cursor-pointer ${statusConfig[t].active}`;
                    } else {
                        btn.className = `status-btn px-4 py-1.5 rounded-full text-xs font-bold transition cursor-pointer ${inactiveClasses}`;
                    }
                }
            });

            // Set keterangan to Belum disimpan
            const ketEl = document.getElementById(`ket-${index}`);
            if (ketEl) {
                ketEl.textContent = 'Belum disimpan';
                ketEl.className = 'text-xs text-amber-600 font-semibold';
            }

            // Recalculate summary stats live
            updateSummaryStats();
        }

        function updateSummaryStats() {
            const rows = document.querySelectorAll('#siswaTableBody tr');
            const total = rows.length;

            if (total === 0) {
                document.getElementById('statHadir').textContent = '0% (0)';
                document.getElementById('statIzin').textContent = '0% (0)';
                document.getElementById('statSakit').textContent = '0% (0)';
                document.getElementById('statAlpa').textContent = '0% (0)';
                document.getElementById('statDispensasi').textContent = '0% (0)';
                return;
            }

            const counts = {
                hadir: 0,
                izin: 0,
                sakit: 0,
                alpa: 0,
                dispensasi: 0
            };

            rows.forEach(r => {
                const status = r.getAttribute('data-status') || 'hadir';
                if (counts[status] !== undefined) {
                    counts[status]++;
                }
            });

            document.getElementById('statHadir').textContent = `${Math.round((counts.hadir / total) * 100)}% (${counts.hadir})`;
            document.getElementById('statIzin').textContent = `${Math.round((counts.izin / total) * 100)}% (${counts.izin})`;
            document.getElementById('statSakit').textContent = `${Math.round((counts.sakit / total) * 100)}% (${counts.sakit})`;
            document.getElementById('statAlpa').textContent = `${Math.round((counts.alpa / total) * 100)}% (${counts.alpa})`;
            document.getElementById('statDispensasi').textContent = `${Math.round((counts.dispensasi / total) * 100)}% (${counts.dispensasi})`;
        }

        // SIMPAN PRESENSI KE DATABASE MYSQL VIA AJAX FETCH
        async function simpanPresensiKeDatabase() {
            const rows = document.querySelectorAll('#siswaTableBody tr');
            if (rows.length === 0) return;

            const pertemuanId = {{ $activePertemuan ? $activePertemuan->id : 'null' }};
            if (!pertemuanId) {
                alert('Pilih pertemuan terlebih dahulu sebelum menyimpan presensi.');
                return;
            }

            const presensiData = {};
            rows.forEach(r => {
                const siswaId = r.getAttribute('data-id');
                const status = r.getAttribute('data-status') || 'hadir';
                if (siswaId) {
                    presensiData[siswaId] = status;
                }
            });

            const btn = document.getElementById('btnSimpanPresensi');
            const spinner = document.getElementById('saveSpinner');
            const btnText = document.getElementById('saveBtnText');

            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Menyimpan...';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch('{{ url("/admin/presensi/simpan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        pertemuan_id: pertemuanId,
                        presensi: presensiData
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Update keterangan di setiap baris menjadi Tersimpan
                    rows.forEach((r, idx) => {
                        const ketEl = document.getElementById(`ket-${idx}`);
                        if (ketEl) {
                            ketEl.textContent = 'Tersimpan';
                            ketEl.className = 'text-xs text-slate-400 font-medium';
                        }
                    });

                    showToast(result.message || 'Data presensi berhasil disimpan ke database!');
                } else {
                    alert('Gagal menyimpan presensi: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (err) {
                console.error(err);
                alert('Gagal menghubungi server untuk menyimpan data.');
            } finally {
                btn.disabled = false;
                spinner.classList.add('hidden');
                btnText.textContent = 'Simpan Presensi';
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('saveToast');
            const msgEl = document.getElementById('toastMessage');
            if (msgEl) msgEl.textContent = msg;

            toast.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
            toast.classList.add('opacity-100', 'translate-y-0');

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }, 3000);
        }

        function openModal(id) {
            document.getElementById(id)?.classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id)?.classList.add('hidden');
        }

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                ['modalTambahSiswa', 'modalTambahPertemuan', 'modalTambahRombel'].forEach(id => closeModal(id));
            }
        });

        // Initialize calculations on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateSummaryStats();
        });
    </script>
</body>
</html>
