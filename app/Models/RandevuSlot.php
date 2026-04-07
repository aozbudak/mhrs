<?php

namespace App\Models;

use App\Enums\RandevuSlotDurumu;
use App\Enums\RandevuSlotTipi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RandevuSlot extends Model
{
    protected $table = 'randevu_slotlari';

    protected $fillable = [
        'doctor_id',
        'baslangic',
        'bitis',
        'durum',
        'slot_tipi',
        'gunluk_degisim_id',
    ];

    protected function casts(): array
    {
        return [
            'baslangic' => 'datetime',
            'bitis' => 'datetime',
            'durum' => RandevuSlotDurumu::class,
            'slot_tipi' => RandevuSlotTipi::class,
        ];
    }

    /**
     * Öncelikli olmayan hastalar yalnızca normal slotları görür / seçebilir.
     */
    public function scopeVisibleForPatient(Builder $query, bool $oncelikliHasta): void
    {
        if ($oncelikliHasta) {
            return;
        }

        $query->where(function (Builder $w) {
            $w->whereNull('slot_tipi')
                ->orWhere('slot_tipi', RandevuSlotTipi::Normal);
        });
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function gunlukDegisim(): BelongsTo
    {
        return $this->belongsTo(GunlukDegisim::class, 'gunluk_degisim_id');
    }

    public function randevu(): HasOne
    {
        return $this->hasOne(Randevu::class, 'randevu_slot_id');
    }
}
