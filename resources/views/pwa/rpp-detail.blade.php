@php
    $isi = (array) $rpp->content_result;

    // Urutan bagian yang paling dicari guru lebih dulu, sisanya menyusul apa adanya
    $urutan = [
        'informasi_umum', 'identifikasi', 'komponen_inti', 'desain_pembelajaran',
        'langkah_pembelajaran', 'kegiatan_pembelajaran', 'asesmen', 'refleksi',
        'integrasi_panca_cinta', 'integrasi_adiwiyata', 'integrasi_kka', 'lampiran',
    ];

    $bagian = collect($isi)
        ->filter(fn ($nilai) => filled($nilai))
        ->sortBy(fn ($nilai, $key) => array_search($key, $urutan) === false ? 99 : array_search($key, $urutan));

    $judulBagian = [
        'informasi_umum' => 'Informasi Umum',
        'identifikasi' => 'Identifikasi Peserta Didik',
        'komponen_inti' => 'Komponen Inti',
        'desain_pembelajaran' => 'Desain Pembelajaran',
        'langkah_pembelajaran' => 'Langkah Pembelajaran',
        'kegiatan_pembelajaran' => 'Kegiatan Pembelajaran',
        'asesmen' => 'Asesmen & Instrumen',
        'refleksi' => 'Refleksi Guru & Murid',
        'integrasi_panca_cinta' => 'Integrasi Panca Cinta',
        'integrasi_adiwiyata' => 'Integrasi Adiwiyata',
        'integrasi_kka' => 'Integrasi Koding & KA',
        'lampiran' => 'Lampiran & LKPD',
    ];
@endphp

<x-pwa-layout title="Isi Modul" active="detail" :detail="$rpp">
    <x-slot name="header">
        <div class="relative z-10 pt-2">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('pwa.rpp.show', $rpp) }}" class="press flex h-11 w-11 shrink-0 items-center justify-center rounded-[18px] bg-white/12 backdrop-blur-md ring-1 ring-white/20 text-white" aria-label="Kembali ke ringkasan">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0 flex-1">
                    <p class="pwa-hero-eyebrow truncate">Isi Modul Lengkap</p>
                    <h1 class="pwa-display pwa-hero-title truncate">{{ $rpp->mata_pelajaran }}</h1>
                </div>
            </div>
            <p class="mt-2 text-[12.5px] leading-5 text-slate-200 line-clamp-2">{{ $rpp->topik }}</p>
        </div>
    </x-slot>

    <!-- Pindah modul yang ditampilkan -->
    @if ($daftarModul->count() > 1)
        <div class="pwa-card pop-in p-4" style="--d: 40ms">
            <label class="pwa-label" for="pindah-modul">Pilih Modul Aktif</label>
            <select id="pindah-modul" class="pwa-field" onchange="if (this.value) window.location.href = this.value">
                @foreach ($daftarModul as $pilihan)
                    <option value="{{ route('pwa.rpp.detail', $pilihan) }}" @selected($pilihan->id === $rpp->id)>
                        {{ $pilihan->mata_pelajaran }} — {{ Str::limit($pilihan->topik, 34) }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    @if ($bagian->isEmpty())
        <div class="pwa-card pop-in p-8 text-center" style="--d: 80ms">
            <div class="mx-auto mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 border border-slate-100 p-2">
                <img src="{{ asset('logo.png') }}" alt="" class="h-10 w-10 object-contain">
            </div>
            <p class="pwa-display text-[15px] font-extrabold text-slate-800">Isi Modul Belum Tersedia</p>
            <p class="pwa-sub mt-1 text-[12px] leading-5 text-slate-500">
                {{ $rpp->status === 'processing' ? 'AI sedang menyusun modul ini. Harap tunggu sebentar.' : 'Silakan generate ulang modul untuk menyusun kontennya.' }}
            </p>
        </div>
    @else
        <!-- Bagian isi modul (Akordion Berurutan) -->
        <div x-data="{ buka: '{{ $bagian->keys()->first() }}' }" class="space-y-3">
            @foreach ($bagian as $key => $nilai)
                <section class="pwa-card pop-in overflow-hidden transition-all duration-200" style="--d: {{ 60 + $loop->index * 35 }}ms"
                    :class="buka === '{{ $key }}' ? 'border-blue-200 ring-2 ring-blue-500/10' : ''">
                    <button type="button" @click="buka = (buka === '{{ $key }}' ? '' : '{{ $key }}')"
                        :aria-expanded="buka === '{{ $key }}'" aria-controls="isi-{{ $loop->index }}"
                        class="press flex w-full items-center justify-between gap-4 p-5 text-left">
                        <span class="flex items-center gap-3.5">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-[11px] font-extrabold"
                                :class="buka === '{{ $key }}' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600'">
                                {{ sprintf('%02d', $loop->iteration) }}
                            </span>
                            <span class="pwa-display text-[13.5px] font-extrabold text-slate-900">
                                {{ $judulBagian[$key] ?? Str::headline($key) }}
                            </span>
                        </span>
                        <svg class="h-4 w-4 shrink-0 transition-transform duration-300" :class="buka === '{{ $key }}' && 'rotate-180 text-blue-600'"
                            style="color: #94A3B8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="isi-{{ $loop->index }}" x-show="buka === '{{ $key }}'" x-transition.opacity.duration.200ms
                        class="border-t p-4 bg-white" style="border-color: var(--line)">
                        <x-pwa-content :data="$nilai" />
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    <!-- Unduh & Dokumen Lengkap -->
    @if ($rpp->status === 'completed')
        <section class="pwa-card pop-in p-5" style="--d: 180ms">
            <h2 class="pwa-h2">Unduh &amp; Cetak</h2>
            <div class="mt-4 grid grid-cols-3 gap-3">
                @foreach ([
                    ['PDF', route('rpp.pdf', $rpp), true],
                    ['Word', route('rpp.word', $rpp), false],
                    ['Cetak', route('rpp.print', $rpp), false]
                ] as [$label, $url, $utama])
                    <a href="{{ $url }}" @if ($label === 'Cetak') target="_blank" @endif
                        class="press rounded-xl py-3 text-center text-[12.5px] font-bold"
                        style="{{ $utama
                            ? 'background: linear-gradient(135deg, #1D4ED8, #2563EB); color: #FFFFFF; box-shadow: var(--sh-brand)'
                            : 'background: #F8FAFC; color: #1E293B; border: 1px solid #E2E8F0' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('pwa.rpp.show', $rpp) }}" data-hide-standalone
                class="press mt-3 block rounded-xl py-3 text-center text-[12px] font-bold border border-blue-200 bg-blue-50/80 text-blue-700 hover:bg-blue-100/80 transition">
                Buka di Ringkasan Detail
            </a>
        </section>
    @endif
</x-pwa-layout>
