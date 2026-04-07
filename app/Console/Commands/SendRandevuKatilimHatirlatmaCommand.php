<?php

namespace App\Console\Commands;

use App\Enums\RandevuDurumu;
use App\Models\Randevu;
use App\Notifications\RandevuKatilimHatirlatmaNotification;
use App\Support\BildirimAyarlari;
use Illuminate\Console\Command;

class SendRandevuKatilimHatirlatmaCommand extends Command
{
    protected $signature = 'randevu:katilim-hatirlatma-gonder';

    protected $description = 'Ayarlandığı kadar gün kala hastalara katılım onayı bildirimi gönderir';

    public function handle(): int
    {
        $daysBefore = BildirimAyarlari::daysBefore();
        $targetStart = now()->addDays($daysBefore)->startOfDay();
        $targetEnd = now()->addDays($daysBefore)->endOfDay();
        $messageTemplate = BildirimAyarlari::messageTemplate();

        $randevular = Randevu::query()
            ->with(['user', 'doctor.user', 'slot'])
            ->whereIn('durum', [RandevuDurumu::Bekliyor, RandevuDurumu::Onaylandi])
            ->whereNull('hatirlatma_bildirildi_at')
            ->whereHas('slot', function ($q) use ($targetStart, $targetEnd) {
                $q->whereBetween('baslangic', [$targetStart, $targetEnd]);
            })
            ->get();

        $adet = 0;
        foreach ($randevular as $randevu) {
            if (! $randevu->user) {
                continue;
            }

            $randevu->user->notify(new RandevuKatilimHatirlatmaNotification($randevu, $messageTemplate));
            $randevu->update([
                'hatirlatma_bildirildi_at' => now(),
            ]);
            $adet++;
        }

        $this->info("Katılım hatırlatma gönderildi: {$adet} (gün: {$daysBefore})");

        return self::SUCCESS;
    }
}
