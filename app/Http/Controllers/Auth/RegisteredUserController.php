<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
<<<<<<< HEAD
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
=======
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create()
>>>>>>> 979a3705ef00246dd71606744f415d8c1390f4cb
    {
        return view('auth.register');
    }

<<<<<<< HEAD
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            // Custom error messages in Bahasa Indonesia
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.max'           => 'Nama lengkap maksimal 255 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'nama'     => $request->name,
            'email'    => $request->email,
            'no_telepon' => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'donatur', // Default role
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role after registration
        return redirect()->intended($this->redirectByRole($user));
    }

    /**
     * Determine the redirect path based on user role.
     */
    protected function redirectByRole(User $user): string
    {
        return match ($user->role) {
            'admin'    => route('desa.daftar'),
            'penyedia' => route('penyedia.dashboard'),
            default    => '/',
        };
=======
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
>>>>>>> 979a3705ef00246dd71606744f415d8c1390f4cb
    }
}
