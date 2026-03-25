@extends('layouts.admin')

@section('title', 'Doktorlar')
@section('subtitle', 'Kayıtlı doktor hesapları ve birim atamaları')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-end gap-2">
        <a href="{{ route('admin.doktorlar.create') }}" class="inline-flex items-center justify-center rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition">
            Yeni doktor ekle
        </a>
    </div>

    <div class="mt-5 rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-sky-50/60 text-xs font-bold text-slate-700">
                    <tr>
                        <th class="px-3 py-2 text-left">Doktor</th>
                        <th class="px-3 py-2 text-left">E-posta</th>
                        <th class="px-3 py-2 text-left">Birim</th>
                        <th class="px-3 py-2 text-left">Ünvan</th>
                        <th class="px-3 py-2 text-left">Durum</th>
                        <th class="px-3 py-2 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-100">
                    @forelse($doktorlar as $d)
                        <tr class="hover:bg-sky-50/40 transition">
                            <td class="px-3 py-3 font-semibold text-slate-900">{{ $d->user?->name ?? trim($d->title ?? '') ?: 'İsimsiz kayıt' }}</td>
                            <td class="px-3 py-3 text-xs text-slate-600">{{ $d->user?->email ?? '—' }}</td>
                            <td class="px-3 py-3 text-xs text-slate-600">{{ $d->department?->name ?? '—' }}</td>
                            <td class="px-3 py-3 text-xs text-slate-600">{{ $d->title ?? '—' }}</td>
                            <td class="px-3 py-3">
                                @if($d->is_active)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-900">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-[11px] font-bold text-slate-800">Pasif</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    @if($d->user)
                                        <a href="{{ route('admin.doktorlar.edit', $d) }}" class="rounded-xl border border-sky-200 bg-sky-50/80 px-2.5 py-1.5 text-[11px] font-semibold text-sky-900 hover:bg-sky-100/80 transition">
                                            Düzenle
                                        </a>
                                    @endif
                                    <form method="post" action="{{ route('admin.doktorlar.destroy', $d) }}" class="inline" onsubmit="return confirm('Bu doktoru ve bağlı kullanıcı hesabını silmek istediğinize emin misiniz? İlgili randevu ve müsait saat kayıları da kaldırılır.');">
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
                                Henüz doktor kaydı yok. «Yeni doktor ekle» ile oluşturabilirsiniz.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($doktorlar->hasPages())
            <div class="mt-4">
                {{ $doktorlar->links() }}
            </div>
        @endif
    </div>
@endsection
