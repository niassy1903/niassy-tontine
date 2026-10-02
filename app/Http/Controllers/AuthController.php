<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View { return view('auth.login'); }
    public function register(): View { return view('auth.register'); }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $key = Str::lower($request->string('email')).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Trop de tentatives. Réessayez dans '.RateLimiter::availableIn($key).' secondes.'])->onlyInput('email');
        }
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::increment($key);
            return back()->withErrors(['email' => 'Ces identifiants ne sont pas valides.'])->onlyInput('email');
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->user()->forceFill(['last_login_at' => now()])->save();
        return redirect()->intended(route('dashboard'))->with('success', 'Bienvenue sur votre espace Niassy.');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create(['name' => $request->string('name'), 'email' => $request->string('email'), 'phone' => $request->string('phone'), 'password' => Hash::make($request->string('password')), 'role' => 'member', 'status' => 'active']);
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('dashboard')->with('success', 'Votre compte a été créé.');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    }
}