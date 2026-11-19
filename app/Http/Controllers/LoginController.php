<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            return redirect()->route(match (true) {
                $user->isAdmin() => 'admin.dashboard',
                $user->isDriver() => 'driver.index',
                default => 'account',
            });
        }

        return view('login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $key = 'login:'.strtolower($credentials['email']).':'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return back()->with('error', 'Terlalu banyak percobaan. Coba lagi dalam 1 menit.');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau kata sandi salah.');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->isDriver()) {
            return redirect()->route('driver.index')
                ->with('success', 'Selamat datang kembali, '.$user->name.'!');
        }

        $fallback = $user->isAdmin() ? route('admin.dashboard') : route('home');

        return redirect()->intended($fallback)
            ->with('success', 'Selamat datang kembali, '.$user->name.'!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
