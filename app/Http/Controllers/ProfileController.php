<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
public function edit()
{
    $user = auth()->user();

    // Jika belum login → pakai dummy user
    if (!$user) {
        $user = (object)[
            'name' => 'Preview User',
            'email' => 'preview@example.com',
            'created_at' => now(),
        ];
    }

    return view('profil.edit', compact('user'));
}

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
