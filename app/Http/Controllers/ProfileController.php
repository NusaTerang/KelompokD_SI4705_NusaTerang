<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
public function edit()
{
    $user = auth()->user() ?? (object)[
        'nama' => 'Preview User',
        'email' => 'preview@example.com',
        'no_telepon' => '081234567890',
        'created_at' => now(),
    ];

    $bergabung = $user->created_at->format('d M Y');

    return view('profil.edit', compact('user', 'bergabung'));
}

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
