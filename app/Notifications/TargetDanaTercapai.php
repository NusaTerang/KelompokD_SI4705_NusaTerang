<?php

namespace App\Notifications;

use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TargetDanaTercapai extends Notification
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
            'title' => 'Target Dana Tercapai',
            'message' => "Target dana proyek {$this->proyek->judul} telah tercapai.",
            'category' => 'funding_target_reached',
            'project_id' => $this->proyek->id,
            'project_title' => $this->proyek->judul,
            'url' => $notifiable->isAdmin()
                ? route('proyek.admin.show', $this->proyek->id)
                : route('proyek.show', $this->proyek->id),
        ];
    }
}
