@extends('layouts.admin')

@section('title', 'Hastaneler')
@section('subtitle', 'Sistemdeki hastane kayıtlarını yönetin')

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:justify-between">
        <form method="get" action="{{ route('admin.hastaneler.index') }}" class="flex flex-wrap items-end gap-2">
            <div>
                <label for="hastane_il_filter" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">İle göre süz</label>
                <select name="city" id="hastane_il_filter" onchange="this.form.submit()" class="min-w-[200px] rounded-2xl border border-sky-200 bg-white px-3 py-2 text-sm font-medium text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <option value="">Tüm iller</option>
                    @foreach ($iller as $il)
                        <option value="{{ $il }}" @selected($filterCity === $il)>{{ $il }}</option>
                    @endforeach
                </select>
            </div>
            @if($filterCity)
                <a href="{{ route('admin.hastaneler.index') }}" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Filtreyi kaldır
                </a>
            @endif
        </form>
        <a href="{{ route('admin.hastaneler.create') }}" class="inline-flex items-center justify-center rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition">
            Yeni hastane ekle
        </a>
    </div>

    <div class="mt-5 rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-sky-50/60 text-xs font-bold text-slate-700">
                    <tr>
                        <th class="px-3 py-2 text-left">Hastane adı</th>
                        <th class="px-3 py-2 text-left">Şehir / İlçe</th>
                        <th class="px-3 py-2 text-left">Telefon</th>
                        <th class="px-3 py-2 text-center">Konum</th>
                        <th class="px-3 py-2 text-left">Durum</th>
                        <th class="px-3 py-2 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-100">
                    @forelse($hastaneler as $hastane)
                        <tr class="hover:bg-sky-50/40 transition">
                            <td class="px-3 py-3 font-semibold text-slate-900">{{ $hastane->name }}</td>
                            <td class="px-3 py-3 text-xs text-slate-600">
                                @php
                                    $konum = collect([
                                        $hastane->city,
                                        ! empty($hastane->districts) ? implode(', ', $hastane->districts) : null,
                                    ])->filter()->implode(' / ');
                                @endphp
                                @if($konum !== '')
                                    {{ $konum }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-3 text-xs text-slate-600">{{ $hastane->phone ?? '—' }}</td>
                            <td class="px-3 py-3 text-center text-xs">
                                @if($hastane->latitude !== null && $hastane->longitude !== null)
                                    <span class="font-medium text-emerald-800" title="{{ $hastane->latitude }}, {{ $hastane->longitude }}">Var</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                @if($hastane->is_active)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-900">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-[11px] font-bold text-slate-800">Pasif</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.hastaneler.edit', $hastane) }}" class="rounded-xl border border-sky-200 bg-sky-50/80 px-2.5 py-1.5 text-[11px] font-semibold text-sky-900 hover:bg-sky-100/80 transition">
                                        Düzenle
                                    </a>
                                    <form method="post" action="{{ route('admin.hastaneler.destroy', $hastane) }}" class="inline" onsubmit="return confirm('Bu hastane kaydını silmek istediğinize emin misiniz?');">
                                        @csrf
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
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-600">
                                Henüz hastane kaydı yok. «Yeni hastane ekle» ile oluşturabilirsiniz.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($hastaneler->hasPages())
            <div class="mt-4">
                {{ $hastaneler->links() }}
            </div>
        @endif
    </div>
@endsection
