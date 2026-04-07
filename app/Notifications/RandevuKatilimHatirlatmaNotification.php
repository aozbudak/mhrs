<?php

namespace App\Notifications;

use App\Models\Randevu;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RandevuKatilimHatirlatmaNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Randevu $randevu,
        private readonly string $messageTemplate
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $doktorAdi = trim((string) ($this->randevu->doctor?->user?->name ?? 'Doktor'));
        $hastaAdi = trim((string) ($notifiable->name ?? 'Hasta'));
        $baslangic = $this->randevu->slot?->baslangic;
        $formattedDate = $baslangic?->translatedFormat('d F Y') ?? '';
        $formattedTime = $baslangic?->translatedFormat('H:i') ?? '';

        $message = strtr($this->messageTemplate, [
            '{doktor}' => $doktorAdi,
            '{hasta}' => $hastaAdi,
            '{tarih}' => $formattedDate,
            '{saat}' => $formattedTime,
        ]);
        if (trim($message) === '') {
            $message = 'Randevunuza gelebilecek misiniz?';
        }

        return [
            'kind' => 'appointment_attendance_check',
            'randevu_id' => $this->randevu->id,
            'title' => 'Randevu katılım onayı',
            'message' => $message,
        ];
    }
}
