<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Daftar pengguna
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search nama atau email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistik
        $totalUsers = User::count();

        $activeUsers = User::where('status', 'aktif')->count();

        $inactiveUsers = User::where('status', 'nonaktif')->count();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers'
        ));
    }

    /**
     * Detail pengguna
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Update role pengguna
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,penyedia,donatur',
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return back()->with(
            'success',
            'Role pengguna berhasil diperbarui.'
        );
    }

    /**
     * Aktifkan / Nonaktifkan akun
     */
    public function toggleStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'aktif'
                ? 'nonaktif'
                : 'aktif'
        ]);

        return back()->with(
            'success',
            'Status pengguna berhasil diperbarui.'
        );
    }
}