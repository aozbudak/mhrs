{{-- @var \Illuminate\Support\Collection<int, \App\Models\Department> $poliklinikler --}}
@if($poliklinikler->isNotEmpty())
    <nav class="mb-4 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-start sm:gap-3" aria-label="Poliklinik filtresi">
        <span class="mt-1.5 shrink-0 text-[11px] font-bold uppercase tracking-wide text-slate-500">Poliklinik</span>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route($routeName) }}"
               class="inline-flex items-center gap-1.5 rounded-2xl border px-3 py-2 text-xs font-semibold transition {{ $seciliPoliklinikId === null ? 'border-sky-500 bg-sky-600 text-white shadow-sm' : 'border-sky-200 bg-white text-slate-800 hover:border-sky-300 hover:bg-sky-50/80' }}">
                Tümü
                <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $seciliPoliklinikId === null ? 'bg-white/20 text-white' : 'bg-sky-100 text-sky-900' }}">{{ $doktorToplam }}</span>
            </a>
            @foreach ($poliklinikler as $pol)
                @php
                    $aktif = $seciliPoliklinikId !== null && (int) $seciliPoliklinikId === (int) $pol->id;
                @endphp
                <a href="{{ route($routeName, ['poliklinik' => $pol->id]) }}"
                   class="inline-flex items-center gap-1.5 rounded-2xl border px-3 py-2 text-xs font-semibold transition {{ $aktif ? 'border-sky-500 bg-sky-600 text-white shadow-sm' : 'border-sky-200 bg-white text-slate-800 hover:border-sky-300 hover:bg-sky-50/80' }}">
                    <span class="max-w-[200px] truncate sm:max-w-none">{{ $pol->name }}</span>
                    <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $aktif ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $pol->hospital_doctor_count }}</span>
                </a>
            @endforeach
        </div>
    </nav>
@endif
