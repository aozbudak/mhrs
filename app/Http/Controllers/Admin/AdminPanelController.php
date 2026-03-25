<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RandevuDurumu;
use App\Http\Controllers\Controller;
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

        $bekleyenToplam = Randevu::query()->where('durum', RandevuDurumu::Bekliyor)->count();

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
            'todayTimeline',
        ));
    }
}

