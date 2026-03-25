<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

final class MusteriAccess
{
    /** Hasta oturumu (web) varsa onu; yoksa yalnız yönetici (admin) oturumunu döner. */
    public static function user(?Request $request = null): ?User
    {
        $request ??= request();

        $web = $request->user('web');
        if ($web instanceof User && $web->isPatient()) {
            return $web;
        }

        $admin = $request->user('admin');
        if ($admin instanceof User && $admin->isAdmin()) {
            return $admin;
        }

        return null;
    }

    public static function allows(?Request $request = null): bool
    {
        return self::user($request) !== null;
    }
}
