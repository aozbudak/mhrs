<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RandevuDurumu;
use App\Enums\RandevuSlotDurumu;
use App\Http\Controllers\Controller;
use App\Models\Randevu;
use App\Models\RandevuSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminRandevuController extends Controller
{
    /** Referer / session olmadan güvenli listeye dönüş (back() bazen /’e düşer). */
    private function redirectToList(Request $request): RedirectResponse
    {
        $durum = $request->input('durum', 'aktif');
        $allowed = ['aktif', 'all', 'bekliyor', 'tamamlandi', 'iptal', 'gelmedi'];
        if (! in_array($durum, $allowed, true)) {
            $durum = 'aktif';
        }

        return redirect()->route('admin.randevular.index', ['durum' => $durum]);
    }

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'durum' => ['nullable', 'in:aktif,all,bekliyor,tamamlandi,iptal,gelmedi'],
        ]);

        $durumFilter = $validated['durum'] ?? 'aktif';

        $q = Randevu::query()
            ->with(['slot', 'doctor.user', 'doctor.department', 'user']);

        match ($durumFilter) {
            'aktif' => $q->where('durum', '!=', RandevuDurumu::Iptal),
            'all' => null,
            default => $q->where('durum', $durumFilter),
        };

        $randevular = $q
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.randevular.index', [
            'randevular' => $randevular,
            'durumFilter' => $durumFilter,
        ]);
    }

    public function tamamla(Request $request, Randevu $randevu): RedirectResponse
    {
        if ($randevu->durum === RandevuDurumu::Tamamlandi) {
            return $this->redirectToList($request)->with('error', 'Bu randevu zaten tamamlandı olarak işaretli.');
        }

        $randevu->update([
            'durum' => RandevuDurumu::Tamamlandi,
            'iptal_nedeni' => null,
        ]);

        return $this->redirectToList($request)->with('success', 'Randevu tamamlandı.');
    }

    public function destroy(Request $request, Randevu $randevu): RedirectResponse
    {
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
