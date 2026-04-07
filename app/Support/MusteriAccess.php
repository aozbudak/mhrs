<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

final class MusteriAccess
{
    /** Sadece hasta guard oturumundaki kullanıcıyı döner. */
    public static function user(?Request $request = null): ?User
    {
        $request ??= request();

        $patient = $request->user('patient');
        if ($patient instanceof User && $patient->isPatient()) {
            return $patient;
        }

        return null;
    }

    public static function allows(?Request $request = null): bool
    {
        return self::user($request) !== null;
    }
}
