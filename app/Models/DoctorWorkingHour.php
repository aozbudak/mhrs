<?php

namespace App\Models;

use Database\Factories\DoctorWorkingHourFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorWorkingHour extends Model
{
    /** @use HasFactory<DoctorWorkingHourFactory> */
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'weekday',
        'start_time',
        'end_time',
        'sort_order',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
