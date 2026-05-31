<?php

namespace App\Notifications;

use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DetailProyekDiisi extends Notification
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
            'title' => 'Detail Proyek Diisi',
            'message' => "Vendor telah mengisi detail proyek {$this->proyek->judul}.",
            'category' => 'vendor_detail_submitted',
            'project_id' => $this->proyek->id,
            'project_title' => $this->proyek->judul,
            'url' => route('proyek.admin.show', $this->proyek->id),
        ];
    }
}
