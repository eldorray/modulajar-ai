<x-pwa-layout :title="$rpp->mata_pelajaran" active="rpp">
    <x-slot name="header">
        <div class="flex items-center gap-3 pt-3">
            <a href="{{ route('pwa.rpp.index') }}" class="press flex h-10 w-10 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="min-w-0">
                <p class="text-[12px] font-medium text-white/70">{{ $rpp->kurikulum }}</p>
                <h1 class="truncate text-[19px] font-extrabold leading-tight">{{ $rpp->mata_pelajaran }}</h1>
            </div>
        </div>
    </x-slot>

    <section class="pwa-card pop-in p-4">
        <h2 class="text-[15px] font-bold">Identitas</h2>
        <dl class="mt-3 space-y-2.5 text-[13px]">
            @foreach ([
                'Topik' => $rpp->topik,
                'Unit' => $rpp->jenjang ?? 'MI',
                'Fase / Kelas' => trim($rpp->fase.' / '.($rpp->kelas ?: '-')),
                'Semester' => $rpp->semester ?: '-',
                'Alokasi' => $rpp->alokasi_waktu.' × '.$rpp->jumlah_pertemuan.' pertemuan',
                'Model' => $rpp->model_pembelajaran,
                'Penyusun' => $rpp->nama_guru,
                'Dibuat' => $rpp->created_at->translatedFormat('d F Y, H:i'),
            ] as $label => $value)
                <div class="flex gap-3">
                    <dt class="w-28 shrink-0 font-semibold text-[#7C90AF]">{{ $label }}</dt>
                    <dd class="flex-1 font-medium">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    @if ($rpp->status === 'completed')
        <section class="pwa-card pop-in p-4" style="--d: 80ms">
            <h2 class="text-[15px] font-bold">Unduh & cetak</h2>
            <div class="mt-3 grid grid-cols-3 gap-2">
                <a href="{{ route('rpp.pdf', $rpp) }}" class="press rounded-xl bg-[#0B4FD9] py-3 text-center text-[12.5px] font-bold text-white shadow-[0_10px_20px_-12px_rgba(11,79,217,.95)]">PDF</a>
                <a href="{{ route('rpp.word', $rpp) }}" class="press rounded-xl border border-[#DDE7F7] bg-[#F8FBFF] py-3 text-center text-[12.5px] font-bold text-[#0B4FD9]">Word</a>
                <a href="{{ route('rpp.print', $rpp) }}" target="_blank" class="press rounded-xl border border-[#DDE7F7] bg-[#F8FBFF] py-3 text-center text-[12.5px] font-bold text-[#0B4FD9]">Cetak</a>
            </div>
            <a href="{{ route('rpp.show', $rpp) }}" class="press mt-3 block rounded-xl bg-[#F2F7FF] py-3 text-center text-[12.5px] font-bold text-[#4A648C]">
                Buka versi lengkap
            </a>
        </section>
    @elseif ($rpp->status === 'processing')
        <section class="pwa-card pop-in p-5 text-center" style="--d: 80ms">
            <div class="mx-auto mb-3 h-10 w-10 animate-spin rounded-full border-4 border-[#DCEAFE] border-t-[#0B4FD9]"></div>
            <p class="text-[14px] font-bold">Masih diproses</p>
            <p class="mt-1 text-[12px] text-[#7C90AF]">Muat ulang halaman ini beberapa saat lagi.</p>
        </section>
    @else
        <section class="pwa-card pop-in p-5 text-center" style="--d: 80ms">
            <p class="text-[14px] font-bold text-rose-600">Generate gagal</p>
            <p class="mt-1 text-[12px] text-[#7C90AF]">Buat ulang modul dengan data yang sama.</p>
            <a href="{{ route('pwa.rpp.create') }}" class="press mt-4 inline-flex rounded-full bg-[#0B4FD9] px-5 py-2.5 text-[13px] font-bold text-white">Coba lagi</a>
        </section>
    @endif

    @php $tujuan = data_get($rpp->content_result, 'komponen_inti.tujuan_pembelajaran') ?? data_get($rpp->content_result, 'desain_pembelajaran.tujuan_pembelajaran'); @endphp
    @if (is_array($tujuan) && $tujuan)
        <section class="pwa-card pop-in p-4" style="--d: 140ms">
            <h2 class="text-[15px] font-bold">Tujuan pembelajaran</h2>
            <ol class="mt-3 space-y-2 text-[13px] leading-6">
                @foreach (array_slice($tujuan, 0, 8) as $idx => $item)
                    <li class="flex gap-2.5">
                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#EAF2FF] text-[10px] font-bold text-[#0B4FD9]">{{ $idx + 1 }}</span>
                        <span>{{ is_array($item) ? ($item['tujuan'] ?? reset($item)) : $item }}</span>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif
</x-pwa-layout>
