<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        $layout = match (auth()->user()->role) {
            'admin' => 'layouts.admin',
            'penyedia' => 'layouts.penyedia',
            default => 'layouts.app',
        };

        return view('notifications.index', compact('notifications', 'layout'));
    }

    public function read(string $id): RedirectResponse
    {
        /** @var DatabaseNotification $notification */
        $notification = auth()->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect($notification->data['url'] ?? route('notifications.index'));
    }

    public function readAll(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index')->with('success', 'Semua notifikasi ditandai dibaca.');
    }
}
