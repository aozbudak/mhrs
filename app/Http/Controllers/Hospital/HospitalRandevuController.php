<?php

namespace App\Http\Controllers\Hospital;

use App\Enums\RandevuDurumu;
use App\Enums\RandevuSlotDurumu;
use App\Http\Controllers\Controller;
use App\Models\Randevu;
use App\Models\RandevuSlot;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HospitalRandevuController extends Controller
{
    protected function institutionRouteGroup(): string
    {
        return 'hastane';
    }

    private function hospitalId(): int
    {
        $user = auth('hospital')->user();
        if (! $user instanceof User || $user->managed_hospital_id === null) {
            abort(403);
        }

        return (int) $user->managed_hospital_id;
    }

    private function redirectToList(Request $request): RedirectResponse
    {
        $durum = $request->input('durum', 'aktif');
        $allowed = ['aktif', 'all', 'bekliyor', 'onaylandi', 'tamamlandi', 'iptal', 'gelmedi'];
        if (! in_array($durum, $allowed, true)) {
            $durum = 'aktif';
        }

        return redirect()->route($this->institutionRouteGroup().'.randevular.index', ['durum' => $durum]);
    }

    private function assertRandevuForHospital(Randevu $randevu, int $hospitalId): void
    {
        $randevu->loadMissing('doctor');
        if (! $randevu->doctor || (int) $randevu->doctor->hospital_id !== $hospitalId) {
            abort(403);
        }
    }

    public function index(Request $request): View
    {
        $hospitalId = $this->hospitalId();

        $validated = $request->validate([
            'durum' => ['nullable', 'in:aktif,all,bekliyor,onaylandi,tamamlandi,iptal,gelmedi'],
        ]);

        $durumFilter = $validated['durum'] ?? 'aktif';

        $q = Randevu::query()
            ->whereHas('doctor', fn ($q2) => $q2->where('hospital_id', $hospitalId))
            ->with(['slot', 'doctor.user', 'doctor.department', 'user']);

        match ($durumFilter) {
            'aktif' => $q->where('durum', '!=', RandevuDurumu::Iptal),
            'all' => null,
            'bekliyor' => $q->whereIn('durum', [RandevuDurumu::Bekliyor, RandevuDurumu::Onaylandi]),
            default => $q->where('durum', $durumFilter),
        };

        $randevular = $q
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return view('hospital.randevular.index', [
            'randevular' => $randevular,
            'durumFilter' => $durumFilter,
            'kr' => $this->institutionRouteGroup(),
        ]);
    }

    public function tamamla(Request $request, Randevu $randevu): RedirectResponse
    {
        $hid = $this->hospitalId();
        $this->assertRandevuForHospital($randevu, $hid);

        if ($randevu->durum === RandevuDurumu::Tamamlandi) {
            return $this->redirectToList($request)->with('error', 'Bu randevu zaten tamamlandı olarak işaretli.');
        }

        if (in_array($randevu->durum, [RandevuDurumu::Iptal, RandevuDurumu::Gelmedi], true)) {
            return $this->redirectToList($request)->with('error', 'Bu randevu tamamlanamaz.');
        }

        $randevu->update([
            'durum' => RandevuDurumu::Tamamlandi,
            'iptal_nedeni' => null,
        ]);

        return $this->redirectToList($request)->with('success', 'Randevu tamamlandı.');
    }

    public function destroy(Request $request, Randevu $randevu): RedirectResponse
    {
        $hid = $this->hospitalId();
        $this->assertRandevuForHospital($randevu, $hid);

        DB::transaction(function () use ($randevu) {
            $slotId = $randevu->randevu_slot_id;

            $randevu->delete();

            $slot = RandevuSlot::query()
                ->whereKey($slotId)
                ->lockForUpdate()
                ->first();

            if ($slot) {
                $slot->update(['durum' => RandevuSlotDurumu::Musait]);
            }
        });

        return $this->redirectToList($request)->with('success', 'Randevu silindi; ilgili saat tekrar müsait olabilir.');
    }
}
