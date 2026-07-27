<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Maksimal percobaan login gagal sebelum di-lockout sementara.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lama lockout dalam detik kalau melebihi batas percobaan.
     */
    private const DECAY_SECONDS = 60;

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $this->ensureIsNotRateLimited($request);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request), self::DECAY_SECONDS);

            $sisaPercobaan = self::MAX_ATTEMPTS - RateLimiter::attempts($this->throttleKey($request));

            $pesan = $sisaPercobaan > 0
                ? "Username atau password yang Anda masukkan salah. Sisa percobaan: {$sisaPercobaan}."
                : 'Username atau password yang Anda masukkan salah.';

            return back()->withErrors(['username' => $pesan])->onlyInput('username');
        }

        if (! Auth::user()->is_active) {
            Auth::logout();
            return back()->withErrors([
                'username' => 'Akun Anda sedang dinonaktifkan. Hubungi admin.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Cegah brute-force: kalau udah kena limit, tolak sebelum sempat cek password ke database.
     */
    private function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), self::MAX_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));
        $menit = ceil($seconds / 60);

        throw ValidationException::withMessages([
            'username' => "Terlalu banyak percobaan login gagal. Coba lagi dalam {$menit} menit.",
        ]);
    }

    /**
     * Kunci pembatasan berdasarkan kombinasi username + alamat IP,
     * biar satu username yang di-brute-force gak sampe ngeblokir user lain di IP yang sama.
     */
    private function throttleKey(Request $request): string
    {
        return Str::lower((string) $request->input('username')) . '|' . $request->ip();
    }
}
