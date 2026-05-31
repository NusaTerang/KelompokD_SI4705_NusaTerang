<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
=======
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
>>>>>>> 979a3705ef00246dd71606744f415d8c1390f4cb
    {
        return view('auth.login');
    }

<<<<<<< HEAD
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Attempt login with "Remember Me" support
        if (! Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Email atau password yang Anda masukkan salah.',
                ]);
        }

        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Redirect based on user role
        return redirect()->intended($this->redirectByRole($user));
    }

    /**
     * Destroy an authenticated session (Logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Determine the redirect path based on user role.
     */
    protected function redirectByRole($user): string
    {
        return match ($user->role) {
            'admin'    => route('desa.daftar'),
            'penyedia' => route('vendor.dashboard'),
            default    => '/',
        };
    }
=======
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
>>>>>>> 979a3705ef00246dd71606744f415d8c1390f4cb
}
