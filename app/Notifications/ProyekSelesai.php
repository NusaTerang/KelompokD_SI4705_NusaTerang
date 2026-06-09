<?php

namespace App\Notifications;

use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProyekSelesai extends Notification
{
    use Queueable;

    public function __construct(public Proyek $proyek) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Proyek Selesai',
            'message' => "Proyek {$this->proyek->judul} telah selesai.",
            'category' => 'project_completed',
            'project_id' => $this->proyek->id,
            'project_title' => $this->proyek->judul,
            'url' => $notifiable->isAdmin()
                ? route('proyek.admin.show', $this->proyek->id)
                : route('proyek.show', $this->proyek->id),
        ];
    }
}
