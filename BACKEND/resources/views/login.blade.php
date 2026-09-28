<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Guru — LMS Indonesia</title>

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
            background-color: #f1f5f9;
        }
    </style>
</head>
<body class="min-h-screen bg-[#F0F4F9] flex flex-col items-center justify-center p-4 sm:p-6 select-none antialiased">

    <!-- Top Heading -->
    <h1 class="text-2xl sm:text-[28px] font-bold text-[#1E293B] text-center mb-8 tracking-tight">
        Portal Guru &mdash; LMS Indonesia
    </h1>

    @php
        $hasAnyError = $errors->any() || request()->has('error');
        $hasEmailError = $errors->has('email') || $errors->has('login') || request()->has('error');
        $hasPasswordError = $errors->has('password') || $errors->has('login') || request()->has('error');
    @endphp

    <!-- Login Card Container -->
    <div class="w-full max-w-[490px] bg-white rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.03)] border border-slate-100 p-8 sm:p-12">
        <!-- Circular Blue Book Icon -->
        <div class="w-12 h-12 rounded-full bg-[#EBF3FE] flex items-center justify-center mx-auto mb-4 text-[#3B82F6]">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
        </div>

        <!-- Card Title -->
        <h2 class="text-xl sm:text-[22px] font-bold text-[#1E293B] text-center tracking-tight">
            Masuk sebagai Guru
        </h2>

        <!-- Subtitle (Dynamic based on Error State) -->
        <p id="loginSubtitle" class="text-xs sm:text-[13px] text-[#94A3B8] text-center mt-1.5 mb-7">
            @if ($hasAnyError)
                Kembali periksa email dan kata sandi Anda
            @else
                Kelola kelas dan pantau presensi siswa
            @endif
        </p>

        <!-- Login Form -->
        <form method="POST" action="{{ url('/login') }}" id="loginForm" class="space-y-4" novalidate>
            @csrf

            <!-- Email Field -->
            <div>
                <label for="email" class="text-xs font-semibold text-[#334155] block mb-1.5">
                    Alamat Email
                </label>
                <div id="emailContainer" class="flex items-center gap-3 w-full px-3.5 py-3 rounded-xl border {{ $hasEmailError ? 'border-red-500' : 'border-slate-200 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/10' }} bg-white transition duration-150">
                    <svg id="emailIcon" class="w-5 h-5 shrink-0 {{ $hasEmailError ? 'text-red-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', request('email', '')) }}"
                        placeholder="nama@sekolah.sch.id"
                        class="w-full text-sm text-slate-800 placeholder-slate-400 outline-none bg-transparent"
                    >
                </div>

                <!-- Error Message Email -->
                @error('email')
                    <p class="text-xs text-red-500 font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="text-xs font-semibold text-[#334155] block mb-1.5 mt-4">
                    Password
                </label>
                <div id="passwordContainer" class="flex items-center gap-3 w-full px-3.5 py-3 rounded-xl border {{ $hasPasswordError ? 'border-red-500' : 'border-slate-200 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/10' }} bg-white transition duration-150">
                    <svg id="passwordIcon" class="w-5 h-5 shrink-0 {{ $hasPasswordError ? 'text-red-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••••••"
                        class="w-full text-sm text-slate-800 placeholder-slate-400 outline-none bg-transparent tracking-wider"
                    >
                    <button type="button" onclick="togglePasswordVisibility()" class="text-slate-400 hover:text-slate-600 focus:outline-none transition p-0.5">
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>

                <!-- Error Message Password & Credential -->
                @error('password')
                    <p class="text-xs text-red-500 font-medium mt-1.5">{{ $message }}</p>
                @enderror
                @error('login')
                    <p class="text-xs text-red-500 font-medium mt-1.5">{{ $message }}</p>
                @enderror
                @if (request()->has('error') && !$errors->any())
                    <p class="text-xs text-red-500 font-medium mt-1.5">Email atau password salah</p>
                @endif
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                id="submitBtn"
                class="w-full py-3 px-4 bg-[#3B82F6] hover:bg-[#2563EB] active:scale-[0.99] text-white font-semibold text-sm rounded-xl transition duration-150 shadow-xs mt-6 cursor-pointer"
            >
                Masuk
            </button>
        </form>
    </div>

    <!-- Back Link -->
    <div class="mt-8 text-center">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Beranda Siswa</span>
        </a>
    </div>

    <!-- Script for Password Visibility and Client-Side Validation Feedback -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                `;
            }
        }

        // Optional: Instant client-side validation check when submitting
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // If empty fields, show instantaneous visual feedback
            if (!email || !password || !emailRegex.test(email) || password.length < 6) {
                // Let the browser submit to trigger backend Laravel validation or handle gracefully
                // This ensures standard Laravel $errors are captured properly
            }
        });
    </script>
</body>
</html>
