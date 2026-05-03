<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    {
        return view('auth.login');
    }

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
            'admin'    => '/admin/dashboard',
            'penyedia' => '/penyedia/dashboard',
            'donatur'  => '/donatur/dashboard',
            default    => '/dashboard',
        };
    }
}
