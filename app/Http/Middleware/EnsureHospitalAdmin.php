<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHospitalAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('hospital');

        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        if ($user->isHospitalAdmin() && ((int) $user->managed_hospital_id) > 0) {
            return $next($request);
        }

        return redirect()
            ->route('login')
            ->with('error', 'Kurum paneli erişimi için hesabınızın bir kuruma (hastane veya sağlık merkezi) atanmış olması gerekir.');
    }
}
