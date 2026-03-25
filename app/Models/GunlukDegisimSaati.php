<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GunlukDegisimSaati extends Model
{
    protected $table = 'gunluk_degisim_saatleri';

    protected $fillable = [
        'gunluk_degisim_id',
        'start_time',
        'end_time',
        'sort_order',
    ];

    public function gunlukDegisim(): BelongsTo
    {
        return $this->belongsTo(GunlukDegisim::class, 'gunluk_degisim_id');
    }
}
