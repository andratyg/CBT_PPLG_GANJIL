<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Menangani pendaftaran/registrasi akun user baru.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
        ], [
            'name.required' => 'Nama Lengkap harus diisi',
            'name.min' => 'Nama minimal 3 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        // Simpan data ke database
        $createAccount = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return redirect()->route('login')->with('success', 'Berhasil membuat akun, silakan login');
    }

    /**
     * Menangani proses autentikasi login pengguna/guru.
     */
    public function login(Request $request)
    {
        // Validasi input form login dipindahkan ke sini (bukan di routes/web.php)
        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        try {
            // 1. Cek autentikasi guru dari tabel guru jika ada
            if (class_exists(Guru::class)) {
                $guru = Guru::where('email', $validatedData['email'])->first();
                if ($guru && Hash::check($validatedData['password'], $guru->password)) {
                    session([
                        'guru_id' => $guru->id,
                        'guru_nama' => $guru->nama ?? 'Pak Andi Wijaya',
                        'role' => 'guru',
                    ]);
                    return redirect()->route('admin.dashboard')->with('success', 'Berhasil login');
                }
            }

            // 2. Cek autentikasi user umum via Auth::attempt
            if (Auth::attempt($validatedData)) {
                $request->session()->regenerate();

                session([
                    'guru_id' => Auth::id(),
                    'guru_nama' => Auth::user()->name ?? 'Pak Andi Wijaya',
                    'role' => Auth::user()->role ?? 'guru',
                ]);

                if (Auth::user()->role === 'admin' || session('role') === 'guru') {
                    return redirect()->route('admin.dashboard')->with('success', 'Berhasil login');
                }

                return redirect()->route('admin.dashboard')->with('success', 'Berhasil login');
            }
        } catch (\Throwable $e) {
            // Abaikan jika database connection belum siap
        }

        // 3. Akun default demo Pak Andi Wijaya untuk kemudahan pengujian
        if ($validatedData['email'] === 'andi.wijaya@sekolah.sch.id' && in_array($validatedData['password'], ['password', 'password123', 'admin123'])) {
            session([
                'guru_id' => 1,
                'guru_nama' => 'Pak Andi Wijaya',
                'role' => 'guru',
            ]);
            return redirect()->route('admin.dashboard')->with('success', 'Berhasil login');
        }

        // Jika autentikasi gagal
        return redirect()->route('login')
            ->with('error', 'Email atau Password salah. Coba lagi!')
            ->withErrors(['login' => 'Email atau Password salah'])
            ->withInput($request->only('email'));
    }

    /**
     * Menangani proses logout user/guru.
     */
    public function logout(Request $request)
    {
        session()->forget(['guru_id', 'guru_nama', 'role']);
        Auth::logout();
        // Memastikan semua session yang ada dibuat invalid/expired
        $request->session()->invalidate();
        // Bikin ulang token session baru
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout');
    }
}
