<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalDepartmentWorkingHour extends Model
{
    protected $fillable = [
        'hospital_id',
        'department_id',
        'weekday',
        'start_time',
        'end_time',
        'sort_order',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
