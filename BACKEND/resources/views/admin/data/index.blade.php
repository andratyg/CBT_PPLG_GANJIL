<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Master - Admin GuruPortal</title>

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

    <!-- jQuery & DataTables CSS & JS CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F8FAFC;
        }

        /* Custom Tailwind Styling for DataTables */
        .dataTables_wrapper {
            padding: 1.25rem;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 0.5rem 0.875rem;
            background-color: #F8FAFC;
            font-size: 0.8125rem;
            color: #1E293B;
            outline: none;
            transition: all 0.2s;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            background-color: #FFFFFF;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
            font-size: 0.8125rem;
            color: #64748B;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #E2E8F0;
            border-radius: 0.625rem;
            padding: 0.35rem 1.75rem 0.35rem 0.75rem;
            background-color: #F8FAFC;
            font-size: 0.8125rem;
            color: #334155;
            outline: none;
            cursor: pointer;
        }
        .dataTables_wrapper .dataTables_info {
            padding-top: 1.25rem;
            font-size: 0.8125rem;
            color: #64748B;
        }
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.625rem !important;
            padding: 0.35rem 0.75rem !important;
            border: 1px solid #E2E8F0 !important;
            background: #FFFFFF !important;
            color: #475569 !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            margin: 0 2px !important;
            transition: all 0.15s ease !important;
            cursor: pointer !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #F1F5F9 !important;
            color: #1E293B !important;
            border-color: #CBD5E1 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #2563EB !important;
            color: #FFFFFF !important;
            border-color: #2563EB !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            background: #F8FAFC !important;
            border-color: #E2E8F0 !important;
        }
        table.dataTable thead th {
            border-bottom: 1px solid #E2E8F0 !important;
            padding: 0.875rem 1.5rem !important;
            font-size: 0.6875rem !important;
            font-weight: 700 !important;
            color: #64748B !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            background-color: #F8FAFC !important;
        }
        table.dataTable tbody td {
            border-top: 1px solid #F1F5F9 !important;
            padding: 0.875rem 1.5rem !important;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid #E2E8F0 !important;
        }
    </style>
</head>
<body class="min-h-screen bg-[#F8FAFC] text-slate-800 flex antialiased selection:bg-blue-100 selection:text-blue-700">

    <!-- SIDEBAR KOMPONEN -->
    <x-sidebar active="master" />

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 ml-64 flex flex-col min-w-0">

        <!-- TOP NAVBAR -->
        <header class="h-20 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-20">
            <!-- Breadcrumb / Section Title -->
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-slate-400 hover:text-slate-600 transition">Dashboard</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Data Master</span>
            </div>

            <!-- Right Profile & Notification -->
            <div class="flex items-center gap-5">
                <button type="button" class="w-10 h-10 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer relative">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span class="w-2 h-2 rounded-full bg-rose-500 absolute top-2.5 right-2.5 ring-2 ring-white"></span>
                </button>

                <div class="h-8 w-px bg-slate-200"></div>

                <!-- Teacher Card -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                        {{ strtoupper(substr($guruNama ?? 'AW', 0, 2)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-bold text-slate-800 leading-tight">{{ $guruNama ?? 'Pak Andi Wijaya' }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">NIP. {{ $guruNip ?? '198204122009031002' }}</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE BODY CONTAINER -->
        <main class="p-8 max-w-7xl w-full mx-auto space-y-6">

            <!-- PAGE TITLE & SUMMARY -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Data Master</h1>
                    <p class="text-xs text-slate-500 mt-1">Kelola data siswa dan bab kurikulum dengan sistem tabel Server-Side DataTables (Yajra).</p>
                </div>
            </div>

            <!-- FLASH ALERTS -->
            @if(session('success'))
                <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200/80 rounded-2xl text-emerald-800 text-xs sm:text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-xs font-bold p-1">✕</button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 px-4 py-3.5 bg-rose-50 border border-rose-200/80 rounded-2xl text-rose-800 text-sm shadow-xs">
                    <div class="flex items-center gap-2.5 mb-1.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span class="font-bold">Terjadi Kesalahan Validasi:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 ml-7">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- MASTER TABS SELECTOR -->
            <div class="flex items-center gap-3 border-b border-slate-200 pb-4 mb-6">
                <!-- Tab Siswa -->
                <a href="{{ route('admin.master', ['tab' => 'siswa']) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2.5 transition {{ $tab === 'siswa' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                    </svg>
                    <span>Data Siswa</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $tab === 'siswa' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $totalSiswa }}</span>
                </a>

                <!-- Tab Bab Materi -->
                <a href="{{ route('admin.master', ['tab' => 'bab']) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2.5 transition {{ $tab === 'bab' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Data Bab Materi</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $tab === 'bab' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $totalBab }}</span>
                </a>
            </div>

            <!-- ========================================================================= -->
            <!-- TAB 1: DATA SISWA (SERVER-SIDE YAJRA DATATABLES)                          -->
            <!-- ========================================================================= -->
            @if($tab === 'siswa')
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <!-- Table Actions Toolbar -->
                    <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                        <!-- Custom Rombel Filter -->
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Filter Rombel:</span>
                            <select id="filterRombel" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                                <option value="">Semua Rombel</option>
                                @foreach($daftarRombel as $r)
                                    <option value="{{ $r }}" {{ $rombel === $r ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2.5">
                            <!-- Tambah Rombel Button -->
                            <button type="button" onclick="openModalTambahRombel()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl border border-slate-200/80 transition flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <span>Tambah Rombel</span>
                            </button>

                            <!-- Tambah Siswa Button -->
                            <button type="button" onclick="openModalTambahSiswa()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <span>Tambah Siswa Baru</span>
                            </button>
                        </div>
                    </div>

                    <!-- Siswa Table (DataTables Server-Side) -->
                    <div class="overflow-x-auto">
                        <table id="siswaTable" class="w-full text-left text-sm text-slate-600">
                            <thead>
                                <tr>
                                    <th class="w-20" title="Nomor urut otomatis sesuai abjad nama">No. Urut</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Rombel</th>
                                    <th>Rayon</th>
                                    <th class="text-right w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- TAB 2: DATA BAB MATERI (SERVER-SIDE YAJRA DATATABLES)                     -->
            <!-- ========================================================================= -->
            @if($tab === 'bab')
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Daftar Bab Kurikulum &amp; Topik Pembelajaran
                        </div>

                        <!-- Tambah Bab Button -->
                        <button type="button" onclick="openModalTambahBab()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Tambah Bab Baru</span>
                        </button>
                    </div>

                    <!-- Bab Table (DataTables Server-Side) -->
                    <div class="overflow-x-auto">
                        <table id="babTable" class="w-full text-left text-sm text-slate-600">
                            <thead>
                                <tr>
                                    <th class="w-16">No</th>
                                    <th>Nama / Judul Bab</th>
                                    <th>Deskripsi Materi</th>
                                    <th class="text-right w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: TAMBAH SISWA                                                       -->
    <!-- ========================================================================= -->
    <div id="modal-tambah-siswa" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-100">
            <button type="button" onclick="closeModal('modal-tambah-siswa')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1">✕</button>
            <h3 class="text-lg font-bold text-slate-900">Tambah Siswa Baru</h3>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan siswa baru ke dalam rombel &amp; database.</p>
            <div class="mt-2 text-[11px] text-blue-600 bg-blue-50/70 border border-blue-100 rounded-lg px-3 py-1.5 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Nomor urut siswa otomatis diurutkan sesuai abjad nama (A &rarr; Z).</span>
            </div>

            <form action="{{ route('admin.master.siswa.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nis" required placeholder="Contoh: 12015" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap Siswa</label>
                    <input type="text" name="nama" required placeholder="Contoh: Rizki Muhammad" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Rombel / Kelas</label>
                        <select name="rombel" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                            <option value="" disabled {{ empty($rombel) ? 'selected' : '' }}>-- Pilih Rombel --</option>
                            @foreach($daftarRombel as $r)
                                <option value="{{ $r }}" {{ $rombel === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rayon</label>
                        <input type="text" name="rayon" required placeholder="Contoh: Cicurug 1" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                    </div>
                </div>
                <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-tambah-siswa')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: EDIT SISWA                                                         -->
    <!-- ========================================================================= -->
    <div id="modal-edit-siswa" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-100">
            <button type="button" onclick="closeModal('modal-edit-siswa')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1">✕</button>
            <h3 class="text-lg font-bold text-slate-900">Perbarui Data Siswa</h3>
            <p class="text-xs text-slate-500 mt-0.5">Ubah informasi siswa di database.</p>

            <form id="form-edit-siswa" method="POST" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIS (Nomor Induk Siswa)</label>
                    <input type="text" id="edit-siswa-nis" name="nis" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap Siswa</label>
                    <input type="text" id="edit-siswa-nama" name="nama" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Rombel / Kelas</label>
                        <select id="edit-siswa-rombel" name="rombel" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                            <option value="" disabled>-- Pilih Rombel --</option>
                            @foreach($daftarRombel as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rayon</label>
                        <input type="text" id="edit-siswa-rayon" name="rayon" required placeholder="Contoh: Cicurug 1" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                    </div>
                </div>
                <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-edit-siswa')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: TAMBAH ROMBEL (KELAS)                                              -->
    <!-- ========================================================================= -->
    <div id="modal-tambah-rombel" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-100">
            <button type="button" onclick="closeModal('modal-tambah-rombel')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1">✕</button>
            <h3 class="text-lg font-bold text-slate-900">Tambah Rombel (Kelas)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan rombongan belajar / kelas baru ke sistem.</p>

            <form action="{{ route('admin.master.rombel.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Rombel / Kelas</label>
                    <input type="text" name="nama" required placeholder="Contoh: XI PPLG 3 atau XII RPL 1" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                </div>
                <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-tambah-rombel')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs">Simpan Rombel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: TAMBAH BAB                                                         -->
    <!-- ========================================================================= -->
    <div id="modal-tambah-bab" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-100">
            <button type="button" onclick="closeModal('modal-tambah-bab')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1">✕</button>
            <h3 class="text-lg font-bold text-slate-900">Tambah Bab Materi Baru</h3>
            <p class="text-xs text-slate-500 mt-0.5">Buat bab pembelajaran kurikulum.</p>

            <form action="{{ route('admin.master.bab.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama / Judul Bab</label>
                    <input type="text" name="nama_bab" required placeholder="Contoh: BAB VI — Menulis Cerita Pendek" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                </div>
                <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-tambah-bab')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs">Simpan Bab</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: EDIT BAB                                                           -->
    <!-- ========================================================================= -->
    <div id="modal-edit-bab" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-100">
            <button type="button" onclick="closeModal('modal-edit-bab')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1">✕</button>
            <h3 class="text-lg font-bold text-slate-900">Perbarui Bab Materi</h3>
            <p class="text-xs text-slate-500 mt-0.5">Ubah nama atau kode bab kurikulum.</p>

            <form id="form-edit-bab" method="POST" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama / Judul Bab</label>
                    <input type="text" id="edit-bab-nama" name="nama_bab" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                </div>
                <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-edit-bab')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT DATATABLES & MODAL HELPERS -->
    <script>
        $(document).ready(function() {
            @if($tab === 'siswa')
                let siswaTable = $('#siswaTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.master.siswa.data') }}",
                        data: function (d) {
                            d.rombel = $('#filterRombel').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'w-16 text-slate-400 font-medium' },
                        { data: 'nis', name: 'nis' },
                        { data: 'nama', name: 'nama' },
                        { data: 'rombel', name: 'rombel' },
                        { data: 'rayon', name: 'rayon' },
                        { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-right' }
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Cari nama, NIS, rayon...",
                        lengthMenu: "Tampilkan _MENU_ siswa",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ siswa",
                        infoEmpty: "Data siswa tidak ditemukan",
                        infoFiltered: "(disaring dari _MAX_ total)",
                        zeroRecords: "Tidak ada siswa yang sesuai",
                        paginate: {
                            previous: "‹",
                            next: "›"
                        },
                        processing: '<div class="text-blue-600 text-xs font-semibold py-2">Memuat data dari server...</div>'
                    },
                    order: [[2, 'asc']] // Sort default nama A-Z
                });

                $('#filterRombel').on('change', function () {
                    siswaTable.draw();
                });
            @endif

            @if($tab === 'bab')
                let babTable = $('#babTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.master.bab.data') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'w-16 text-slate-400 font-medium' },
                        { data: 'nama_bab', name: 'nama_bab' },
                        { data: 'deskripsi', name: 'deskripsi' },
                        { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-right' }
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Cari nama bab materi...",
                        lengthMenu: "Tampilkan _MENU_ bab",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ bab",
                        infoEmpty: "Data bab tidak ditemukan",
                        infoFiltered: "(disaring dari _MAX_ total)",
                        zeroRecords: "Tidak ada bab yang sesuai",
                        paginate: {
                            previous: "‹",
                            next: "›"
                        },
                        processing: '<div class="text-blue-600 text-xs font-semibold py-2">Memuat data dari server...</div>'
                    },
                    order: [[1, 'asc']]
                });
            @endif
        });

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function openModalTambahRombel() {
            document.getElementById('modal-tambah-rombel').classList.remove('hidden');
        }

        function openModalTambahSiswa() {
            document.getElementById('modal-tambah-siswa').classList.remove('hidden');
        }

        function openModalEditSiswa(id, nis, nama, rombel, rayon) {
            document.getElementById('form-edit-siswa').action = `/admin/master/siswa/${id}`;
            document.getElementById('edit-siswa-nis').value = nis;
            document.getElementById('edit-siswa-nama').value = nama;

            const rombelSelect = document.getElementById('edit-siswa-rombel');
            if (rombel && !Array.from(rombelSelect.options).some(opt => opt.value === rombel)) {
                const opt = new Option(rombel, rombel, true, true);
                rombelSelect.add(opt);
            }
            rombelSelect.value = rombel;

            document.getElementById('edit-siswa-rayon').value = rayon;
            document.getElementById('modal-edit-siswa').classList.remove('hidden');
        }

        function openModalTambahBab() {
            document.getElementById('modal-tambah-bab').classList.remove('hidden');
        }

        function openModalEditBab(id, namaBab) {
            document.getElementById('form-edit-bab').action = `/admin/master/bab/${id}`;
            document.getElementById('edit-bab-nama').value = namaBab;
            document.getElementById('modal-edit-bab').classList.remove('hidden');
        }
    </script>
</body>
</html>
