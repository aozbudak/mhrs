<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RandevuDurumu;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Randevu;
use Illuminate\View\View;

class AdminPanelController extends Controller
{
    public function index(): View
    {
        $now = now();

        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        $todayCount = Randevu::query()
            ->where('durum', '!=', RandevuDurumu::Iptal)
            ->whereHas('slot', function ($q) use ($todayStart, $todayEnd) {
                $q->whereBetween('baslangic', [$todayStart, $todayEnd]);
            })
            ->count();

        $bekleyenToplam = Randevu::query()
            ->whereIn('durum', [RandevuDurumu::Bekliyor, RandevuDurumu::Onaylandi])
            ->count();

        $doktorSayisi = Doctor::query()->where('is_active', true)->count();
        $hastaneSayisi = Hospital::query()->where('is_active', true)->count();

        $tamamlananBugun = Randevu::query()
            ->where('durum', RandevuDurumu::Tamamlandi)
            ->whereHas('slot', function ($q) use ($todayStart, $todayEnd) {
                $q->whereBetween('baslangic', [$todayStart, $todayEnd]);
            })
            ->count();

        $dolulukOraniBugun = $todayCount > 0
            ? (int) round(($tamamlananBugun / $todayCount) * 100)
            : 0;

        $todayTimeline = Randevu::query()
            ->select('randevular.*')
            ->join('randevu_slotlari', 'randevular.randevu_slot_id', '=', 'randevu_slotlari.id')
            ->whereBetween('randevu_slotlari.baslangic', [$todayStart, $todayEnd])
            ->where('randevular.durum', '!=', RandevuDurumu::Iptal)
            ->orderBy('randevu_slotlari.baslangic')
            ->with(['slot', 'doctor.user', 'doctor.department', 'user'])
            ->limit(24)
            ->get();

        return view('admin.panel', compact(
            'todayCount',
            'bekleyenToplam',
            'doktorSayisi',
            'hastaneSayisi',
            'tamamlananBugun',
            'dolulukOraniBugun',
            'todayTimeline',
        ));
    }
}
