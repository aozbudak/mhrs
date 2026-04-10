<?php

namespace App\Http\Middleware;

use App\Models\Hospital;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagedInstitutionKind
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $kind  "hospital" | "saglik_merkezi"
     */
    public function handle(Request $request, Closure $next, string $kind): Response
    {
        $user = $request->user('hospital');

        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        $hid = (int) $user->managed_hospital_id;
        if ($hid < 1) {
            return redirect()->route('login');
        }

        /** @var Hospital|null $hospital */
        $hospital = Hospital::query()->whereKey($hid)->first();
        if (! $hospital) {
            abort(403);
        }

        if ($kind === 'hospital' && $hospital->is_saglik_merkezi) {
            return redirect()
                ->route('saglik-merkezi.panel')
                ->with('error', 'Bu bölüm hastane kurumu içindir. Sağlık merkezi paneline yönlendirildiniz.');
        }

        if ($kind === 'saglik_merkezi' && ! $hospital->is_saglik_merkezi) {
            return redirect()
                ->route('hastane.panel')
                ->with('error', 'Bu bölüm sağlık merkezi içindir. Hastane paneline yönlendirildiniz.');
        }

        return $next($request);
    }
}
