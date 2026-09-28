<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuruAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Izinkan jika ada session guru_id atau autentikasi user bawaan
        if (!session()->has('guru_id') && !auth()->check()) {
            return redirect()->route('login')->withErrors([
                'login' => 'Silakan masuk terlebih dahulu untuk mengakses dashboard admin guru.',
            ]);
        }

        return $next($request);
    }
}
