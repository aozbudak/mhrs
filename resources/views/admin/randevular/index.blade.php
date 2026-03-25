@extends('layouts.admin')

@section('title', 'Randevular')
@section('subtitle', 'Hasta randevuları burada listelenir; varsayılan: iptal edilmemiş kayıtlar · sayfa başına 30')

@section('content')
    <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
        <form method="get" action="{{ route('admin.randevular.index') }}" class="flex flex-wrap items-end justify-end gap-2">
                <div>
                    <label for="durum" class="block text-[11px] font-bold text-slate-600">Durum</label>
                    <select name="durum" id="durum" class="mt-1 rounded-2xl border border-sky-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                        <option value="aktif" @selected($durumFilter === 'aktif')>Aktif (iptal hariç)</option>
                        <option value="all" @selected($durumFilter === 'all')>Tümü</option>
                        <option value="bekliyor" @selected($durumFilter === 'bekliyor')>Bekliyor</option>
                        <option value="tamamlandi" @selected($durumFilter === 'tamamlandi')>Tamamlandı</option>
                        <option value="iptal" @selected($durumFilter === 'iptal')>İptal</option>
                        <option value="gelmedi" @selected($durumFilter === 'gelmedi')>Gelmedi</option>
                    </select>
                </div>
                <button type="submit" class="rounded-2xl border border-sky-200 bg-sky-50/80 px-4 py-2 text-sm font-semibold text-sky-900 hover:bg-sky-100/80 transition">
                    Filtrele
                </button>
        </form>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-sky-50/60 text-xs font-bold text-slate-700">
                    <tr>
                        <th class="px-3 py-2 text-left">Slot tarihi</th>
                        <th class="px-3 py-2 text-left">Hasta</th>
                        <th class="px-3 py-2 text-left">Doktor</th>
                        <th class="px-3 py-2 text-left">Birim</th>
                        <th class="px-3 py-2 text-left">Durum</th>
                        <th class="px-3 py-2 text-left">Kayıt</th>
                        <th class="px-3 py-2 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-100">
                    @forelse($randevular as $a)
                        @php
                            $durumRaw = $a->getRawOriginal('durum') ?? 'bekliyor';
                            $badgeClass = match ($durumRaw) {
                                'bekliyor' => 'bg-amber-100 text-amber-900',
                                'tamamlandi' => 'bg-emerald-100 text-emerald-900',
                                'iptal' => 'bg-sky-200 text-sky-950',
                                'gelmedi' => 'bg-rose-100 text-rose-900',
                                default => 'bg-slate-100 text-slate-700',
                            };
                            $durumLabel = match ($durumRaw) {
                                'bekliyor' => 'Bekliyor',
                                'tamamlandi' => 'Tamamlandı',
                                'iptal' => 'İptal',
                                'gelmedi' => 'Gelmedi',
                                default => $durumRaw,
                            };
                        @endphp
                        <tr class="hover:bg-sky-50/40 transition">
                            <td class="px-3 py-3 text-xs text-slate-700">
                                @if($a->slot?->baslangic)
                                    {{ $a->slot->baslangic->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-3 font-semibold text-slate-900">{{ $a->user?->name ?? '—' }}</td>
                            <td class="px-3 py-3 text-slate-800">{{ $a->doctor?->user?->name ?? trim($a->doctor?->title ?? '') ?: '—' }}</td>
                            <td class="px-3 py-3 text-xs text-slate-600">{{ $a->doctor?->department?->name ?? '—' }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold {{ $badgeClass }}">{{ $durumLabel }}</span>
                            </td>
                            <td class="px-3 py-3 text-xs text-slate-600">
                                {{ $a->created_at?->timezone(config('app.timezone'))->format('d.m.Y H:i') ?? '—' }}
                            </td>
                            <td class="px-3 py-3 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    @if ($a->durum !== \App\Enums\RandevuDurumu::Tamamlandi)
                                        <form method="post" action="{{ route('admin.randevular.tamamla', $a) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="durum" value="{{ $durumFilter }}">
                                            <button type="submit" class="rounded-xl border border-emerald-200 bg-emerald-50/80 px-2.5 py-1.5 text-[11px] font-semibold text-emerald-800 hover:bg-emerald-100/80 transition">
                                                Tamamla
                                            </button>
                                        </form>
                                    @endif
                                    <form method="post" action="{{ route('admin.randevular.destroy', $a) }}" class="inline" onsubmit="return confirm('Bu randevuyu kalıcı olarak silmek istediğinize emin misiniz? Saat müsait hale getirilecek.');">
                                        @csrf
                                        <input type="hidden" name="durum" value="{{ $durumFilter }}">
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-red-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-red-600 hover:bg-red-50/80 transition">
                                            Sil
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-sm text-slate-600">
                                Henüz randevu kaydı yok veya filtreye uyan sonuç bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($randevular->hasPages())
            <div class="mt-4">
                {{ $randevular->links() }}
            </div>
        @endif
    </div>
@endsection
