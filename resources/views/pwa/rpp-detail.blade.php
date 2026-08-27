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
        'informasi_umum' => 'Informasi umum',
        'identifikasi' => 'Identifikasi',
        'komponen_inti' => 'Komponen inti',
        'desain_pembelajaran' => 'Desain pembelajaran',
        'langkah_pembelajaran' => 'Langkah pembelajaran',
        'kegiatan_pembelajaran' => 'Kegiatan pembelajaran',
        'asesmen' => 'Asesmen',
        'refleksi' => 'Refleksi',
        'integrasi_panca_cinta' => 'Integrasi Panca Cinta',
        'integrasi_adiwiyata' => 'Integrasi Adiwiyata',
        'integrasi_kka' => 'Integrasi Koding & KA',
        'lampiran' => 'Lampiran',
    ];
@endphp

<x-pwa-layout title="Isi Modul" active="detail" :detail="$rpp">
    <x-slot name="header">
        <div class="relative z-10 pt-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('pwa.rpp.show', $rpp) }}" class="press flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0 flex-1">
                    <p class="pwa-hero-eyebrow truncate">Isi modul lengkap</p>
                    <h1 class="pwa-display pwa-hero-title truncate">{{ $rpp->mata_pelajaran }}</h1>
                </div>
            </div>
            <p class="mt-2.5 text-[12.5px] leading-5 text-white/85">{{ $rpp->topik }}</p>
        </div>
    </x-slot>

    <!-- Pindah modul yang ditampilkan -->
    @if ($daftarModul->count() > 1)
        <div class="pwa-card pop-in p-3">
            <label class="pwa-label" for="pindah-modul">Tampilkan modul</label>
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
        <div class="pwa-card pop-in p-7 text-center">
            <img src="{{ asset('logo.png') }}" alt="" class="float mx-auto mb-3 h-14 w-14 object-contain">
            <p class="pwa-display text-[14.5px] font-extrabold">Isi modul belum tersedia</p>
            <p class="pwa-sub mt-1 text-[12px] leading-5">
                {{ $rpp->status === 'processing' ? 'AI masih menyusun modul ini.' : 'Generate ulang modul untuk mengisi kontennya.' }}
            </p>
        </div>
    @else
        <!-- Bagian isi modul, terbuka satu per satu -->
        <div x-data="{ buka: '{{ $bagian->keys()->first() }}' }" class="space-y-3.5">
            @foreach ($bagian as $key => $nilai)
                <section class="pwa-card pop-in overflow-hidden" style="--d: {{ $loop->index * 55 }}ms">
                    <button type="button" @click="buka = (buka === '{{ $key }}' ? '' : '{{ $key }}')"
                        class="press flex w-full items-center justify-between gap-3 px-4 py-3.5 text-left">
                        <span class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-[11px] font-extrabold"
                                style="background: var(--brand-50); color: var(--brand-700)">{{ $loop->iteration }}</span>
                            <span class="pwa-display text-[13.5px] font-extrabold">
                                {{ $judulBagian[$key] ?? Str::headline($key) }}
                            </span>
                        </span>
                        <svg class="h-5 w-5 shrink-0 transition-transform duration-300" style="color: #A9BBD6"
                            :class="buka === '{{ $key }}' && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>

                    <div x-show="buka === '{{ $key }}'" x-transition.origin.top class="border-t px-4 py-4" style="border-color: var(--line)">
                        <x-pwa-content :data="$nilai" />
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    <!-- Unduh -->
    @if ($rpp->status === 'completed')
        <section class="pwa-card pop-in p-4" style="--d: 200ms">
            <h2 class="pwa-h2">Unduh &amp; cetak</h2>
            <div class="mt-3 grid grid-cols-3 gap-2">
                @foreach ([['PDF', route('rpp.pdf', $rpp), true], ['Word', route('rpp.word', $rpp), false], ['Cetak', route('rpp.print', $rpp), false]] as [$label, $url, $utama])
                    <a href="{{ $url }}" @if ($label === 'Cetak') target="_blank" @endif
                        class="press rounded-2xl py-3 text-center text-[12.5px] font-bold"
                        style="{{ $utama
                            ? 'background: linear-gradient(150deg, var(--brand-700), var(--brand-500)); color: #fff; box-shadow: var(--sh-brand)'
                            : 'background: #F7FAFF; color: var(--brand-700); box-shadow: inset 0 0 0 1px var(--line)' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('rpp.show', $rpp) }}" data-hide-standalone
                class="press mt-2.5 block rounded-2xl py-3 text-center text-[12.5px] font-bold"
                style="background: var(--brand-50); color: var(--brand-700)">
                Buka di tampilan desktop
            </a>
        </section>
    @endif
</x-pwa-layout>
