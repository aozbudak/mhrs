<?php

namespace App\Models;

use App\Enums\RandevuSlotDurumu;
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
        'gunluk_degisim_id',
    ];

    protected function casts(): array
    {
        return [
            'baslangic' => 'datetime',
            'bitis' => 'datetime',
            'durum' => RandevuSlotDurumu::class,
        ];
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
