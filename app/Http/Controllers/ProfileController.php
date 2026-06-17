<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\Donasi;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        $bergabung = $user->created_at->format('d M Y');

        $riwayatDonasi = Donasi::with('proyek')
            ->where('id_donatur', $user->id_donatur)
            ->orderByDesc('created_at')
            ->get();

        return view('profil.edit', compact(
            'user',
            'bergabung',
            'riwayatDonasi'
        ));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('profil.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
    public function detailDonasi($id)
    {
        $donasi = Donasi::with('proyek')
            ->where('id_donasi', $id)
            ->where('id_donatur', auth()->user()->id_donatur)
            ->firstOrFail();

        return view('profil.detail-donasi', compact('donasi'));
    }
}