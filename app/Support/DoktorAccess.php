<?php

namespace App\Support;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;

final class DoktorAccess
{
    /** Doktor guard oturumundaki aktif doktor kaydı (yoksa null). */
    public static function doctor(?Request $request = null): ?Doctor
    {
        $request ??= request();
        $doctorUser = $request->user('doctor');
        if (! $doctorUser instanceof User || ! $doctorUser->isDoctor()) {
            return null;
        }

        $doctor = $doctorUser->doctor;
        if (! $doctor || ! $doctor->is_active) {
            return null;
        }

        return $doctor;
    }
}
