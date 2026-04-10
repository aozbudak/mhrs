<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('admin');

        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isHospitalAdmin()) {
            return redirect()
                ->route('hastane.panel')
                ->with('error', 'Kurum yöneticisi hesabıyla yönetim paneline girilemez.');
        }

        if ($user->isPatient()) {
            return redirect()
                ->route('musteri.panel')
                ->with('error', 'Bu bölüm yalnızca yönetici hesapları içindir.');
        }

        return redirect()
            ->route('login')
            ->with('error', 'Hesap rolü tanınmıyor. Lütfen yönetici ile iletişime geçin.');
    }
}
