<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        $user = request()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('profile');
        }

        $localVerificationUrl = app()->isLocal()
            ? URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ])
            : null;

        return view('auth.verify-email', compact('localVerificationUrl'));
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('interests.edit')->with('status', 'Tu correo fue verificado. Ahora dinos qué te inspira.');
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
        $user = request()->user()->load(['checkIns' => fn ($query) => $query->with('place:id,name,slug')->latest('visited_on')->limit(10)]);
        $user->loadCount([
            'checkIns as verified_check_ins_count' => fn ($query) => $query->where('status', 'verified'),
            'checkIns as pending_check_ins_count' => fn ($query) => $query->where('status', 'pending'),
        ]);

        return view('profile.show', compact('user'));
    }
}
