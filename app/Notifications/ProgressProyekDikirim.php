<?php

namespace App\Notifications;

use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProgressProyekDikirim extends Notification
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
            'title' => 'Update Progress Proyek',
            'message' => "Progress proyek {$this->proyek->judul} telah diperbarui.",
            'category' => 'project_progress',
            'project_id' => $this->proyek->id,
            'project_title' => $this->proyek->judul,
            'url' => $notifiable->isAdmin()
                ? route('proyek.admin.show', $this->proyek->id)
                : route('proyek.show', $this->proyek->id),
        ];
    }
}
