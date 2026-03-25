<?php

namespace App\Http\Middleware;

use App\Support\MusteriAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePatient
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! MusteriAccess::allows($request)) {
            return redirect()
                ->route('login')
                ->with('error', 'Bu alan yalnızca hastalar veya yetkili yöneticiler içindir. Lütfen giriş yapın.');
        }

        return $next($request);
    }
}
