<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function register(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:191', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Debes aceptar los Términos y la Política de Privacidad.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'password' => $validated['password'],
            'role' => 'traveler',
            'terms_accepted_at' => now(),
            'terms_version' => config('app.terms_version', '2026-08-25'),
        ]);

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice');
    }

    public function login(): View
    {
        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = 'login:'.Str::lower($request->string('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Demasiados intentos. Intenta nuevamente en '.RateLimiter::availableIn($key).' segundos.'])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            return back()->withErrors(['email' => 'El correo o la contraseña no coinciden.'])->onlyInput('email');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('profile'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function verificationNotice(): View|RedirectResponse
    {
        return request()->user()->hasVerifiedEmail()
            ? redirect()->route('profile')
            : view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('profile')->with('status', 'Tu correo fue verificado correctamente.');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('profile');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Enviamos un nuevo enlace de verificación.');
    }

    public function profile(): View
    {
        return view('profile.show');
    }
}
