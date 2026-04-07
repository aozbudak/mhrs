@extends('layouts.musteri')

@section('title', 'Bildirimler')

@section('content')
    <section class="rounded-3xl border border-sky-100/80 bg-white/70 p-5 shadow-sm hospital-glass">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-extrabold text-slate-900">Bildirimlerim</h2>
            <span class="text-xs font-semibold text-slate-500">{{ $notifications?->total() ?? 0 }} kayıt</span>
        </div>

        <div class="mt-4 space-y-2">
            @forelse($notifications ?? [] as $n)
                <div class="rounded-2xl border border-sky-100/80 bg-white/70 px-4 py-3 {{ $n->read_at ? 'opacity-80' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-semibold text-slate-900">{{ $n->data['title'] ?? 'Bildirim' }}</div>
                            <div class="mt-1 text-sm text-slate-600">{{ $n->data['message'] ?? $n->type }}</div>
                        </div>
                        @if($n->read_at)
                            <span class="shrink-0 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-900">Okundu</span>
                        @else
                            <span class="shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-900">Yeni</span>
                        @endif
                    </div>

                    @if (! $n->read_at && ($n->data['kind'] ?? null) === 'appointment_attendance_check' && isset($n->data['randevu_id']))
                        <div class="mt-3 flex items-center gap-2">
                            <form method="post" action="{{ route('musteri.randevu.katilim-bildirimi') }}">
                                @csrf
                                <input type="hidden" name="randevu_id" value="{{ (int) $n->data['randevu_id'] }}">
                                <input type="hidden" name="notification_id" value="{{ $n->id }}">
                                <input type="hidden" name="yanit" value="gelecek">
                                <button type="submit" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                                    Geleceğim
                                </button>
                            </form>
                            <form method="post" action="{{ route('musteri.randevu.katilim-bildirimi') }}">
                                @csrf
                                <input type="hidden" name="randevu_id" value="{{ (int) $n->data['randevu_id'] }}">
                                <input type="hidden" name="notification_id" value="{{ $n->id }}">
                                <input type="hidden" name="yanit" value="gelemeyecek">
                                <button type="submit" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">
                                    Gelemeyeceğim
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="mt-2 text-[11px] text-slate-400">
                        {{ $n->created_at?->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-sky-200 bg-white/60 py-10 text-center text-sm text-slate-500">
                    Henüz bildiriminiz yok.
                </div>
            @endforelse
        </div>

        @if($notifications)
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </section>
@endsection
