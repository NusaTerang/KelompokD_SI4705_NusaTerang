<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
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
=======
use Illuminate\Http\Request;

/** Stub — akan diimplementasi oleh tim */
class ProfileController extends Controller
{
    public function edit(Request $request) { abort(501, 'Belum diimplementasi'); }
    public function update(Request $request) { abort(501, 'Belum diimplementasi'); }
>>>>>>> 979a3705ef00246dd71606744f415d8c1390f4cb
}
