<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalDepartmentSetting extends Model
{
    protected $fillable = [
        'hospital_id',
        'department_id',
        'randevu_slot_dakika',
        'senior_age_threshold',
        'auto_transfer_senior',
        'mesai_tasima_aktif',
        'ameliyat_blok_baslangic',
        'ameliyat_blok_bitis',
    ];

    protected function casts(): array
    {
        return [
            'randevu_slot_dakika' => 'integer',
            'senior_age_threshold' => 'integer',
            'auto_transfer_senior' => 'boolean',
            'mesai_tasima_aktif' => 'boolean',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
