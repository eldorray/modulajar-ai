@props(['data', 'level' => 0])

@php
    /** Ubah kunci JSON jadi label yang enak dibaca. */
    $labelkan = function (string $key): string {
        $singkatan = [
            'lkpd' => 'LKPD', 'kka' => 'KKA', 'kse' => 'KSE', 'tp' => 'TP', 'pg' => 'PG',
            'cp' => 'CP', 'dpl' => 'DPL', 'sts' => 'STS', 'ai' => 'AI', 'hots' => 'HOTS',
            'lots' => 'LOTS', 'mots' => 'MOTS', 'tpack' => 'TPACK', 'rpp' => 'RPP',
        ];

        return collect(explode('_', $key))
            ->map(fn ($kata) => $singkatan[mb_strtolower($kata)] ?? mb_convert_case($kata, MB_CASE_TITLE, 'UTF-8'))
            ->implode(' ');
    };

    $adalahDaftar = is_array($data) && array_is_list($data);
    $semuaSkalar = $adalahDaftar && collect($data)->every(fn ($item) => ! is_array($item));
@endphp

@if (blank($data) && $data !== 0 && $data !== '0')
    {{-- Bagian kosong tidak ditampilkan --}}
@elseif (! is_array($data))
    <p class="text-[12.5px] leading-5 text-[#3E5B87]">{{ is_bool($data) ? ($data ? 'Ya' : 'Tidak') : $data }}</p>
@elseif ($semuaSkalar)
    <ul class="space-y-1.5">
        @foreach ($data as $item)
            <li class="flex gap-2 text-[12.5px] leading-5 text-[#3E5B87]">
                <span class="mt-[7px] h-1.5 w-1.5 shrink-0 rounded-full" style="background: var(--brand-500)"></span>
                <span>{{ $item }}</span>
            </li>
        @endforeach
    </ul>
@elseif ($adalahDaftar)
    <div class="space-y-2">
        @foreach ($data as $i => $item)
            @php
                // Pakai penanda milik item (nomor/kode/judul) sebagai kepala kartu
                $kunciJudul = collect(['nomor', 'no', 'kode', 'judul', 'nama', 'fase', 'fase_sintaks'])
                    ->first(fn ($k) => is_array($item) && filled($item[$k] ?? null) && ! is_array($item[$k]));
                $kepala = $kunciJudul ? $item[$kunciJudul] : $i + 1;
                $sisa = $kunciJudul ? collect($item)->except($kunciJudul)->all() : $item;
            @endphp
            <div class="rounded-2xl p-3" style="background: {{ $level % 2 === 0 ? '#F7FAFF' : '#fff' }}; box-shadow: inset 0 0 0 1px var(--line)">
                <p class="mb-1.5 text-[11px] font-extrabold" style="color: var(--brand-700)">{{ $kepala }}</p>
                <x-pwa-content :data="$sisa" :level="$level + 1" />
            </div>
        @endforeach
    </div>
@else
    <div class="space-y-2.5">
        @foreach ($data as $key => $nilai)
            @continue(blank($nilai) && $nilai !== 0 && $nilai !== '0')

            @if (is_array($nilai))
                <div>
                    <p class="mb-1.5 text-[11.5px] font-extrabold" style="color: var(--ink)">{{ $labelkan((string) $key) }}</p>
                    <x-pwa-content :data="$nilai" :level="$level + 1" />
                </div>
            @else
                @php
                    $teks = is_bool($nilai) ? ($nilai ? 'Ya' : 'Tidak') : (string) $nilai;
                    // Nilai panjang butuh lebar penuh; label pindah ke atas
                    $panjang = mb_strlen($teks) > 70;
                @endphp
                <div class="{{ $panjang ? '' : 'flex gap-2.5' }}">
                    <span class="{{ $panjang ? 'mb-1 block' : 'w-[92px] shrink-0' }} text-[10.5px] font-bold uppercase leading-5 tracking-wide"
                        style="color: var(--muted)">
                        {{ $labelkan((string) $key) }}
                    </span>
                    <span class="{{ $panjang ? 'block' : 'flex-1' }} text-[12.5px] leading-5 text-[#3E5B87]">
                        {{ $teks }}
                    </span>
                </div>
            @endif
        @endforeach
    </div>
@endif
