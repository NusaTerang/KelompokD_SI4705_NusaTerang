<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        $bergabung = $user->created_at
            ? $user->created_at->locale('id')->translatedFormat('F Y')
            : '—';

        return view('profil.edit', [
            'user' => $user,
            'bergabung' => $bergabung,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
