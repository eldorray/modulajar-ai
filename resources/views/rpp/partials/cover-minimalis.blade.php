{{-- Sampul Minimalis — tanpa ornamen dan tanpa blok warna, hanya garis rambut.

     Nama mata pelajaran memakai $primary, bukan $accent seperti dua sampul lain:
     warna aksen (kuning/oranye) terlalu terang untuk sampul yang gaya bacanya
     tenang, dan tetap konsisten dengan tabel garis pada skin ini. --}}
<div class="cover">
    @if(isset($schoolSettings) && $schoolSettings->logo)
    <div class="cover-school-logo">
        <img src="{{ $isPrint ? asset('storage/' . $schoolSettings->logo) : storage_path('app/public/' . $schoolSettings->logo) }}" alt="Logo">
    </div>
    @endif

    <div style="font-size: 11pt; text-transform: uppercase; letter-spacing: 3px; color: #374151;">{{ strtoupper($schoolName) }}</div>
    @if($schoolCity)
    <div style="font-size: 9pt; color: #6b7280; letter-spacing: 2px;">{{ strtoupper($schoolCity) }}</div>
    @endif

    <div style="border-top: 1px solid {{ $primary }}; width: 45%; margin: 26px auto;"></div>

    <div style="font-size: 18pt; text-transform: uppercase; line-height: 1.3; letter-spacing: 2px; color: #4b5563;">
        RENCANA PELAKSANAAN<br>PEMBELAJARAN MENDALAM<br><span style="font-size: 11pt; letter-spacing: 1px;">{{ strtoupper($rpp->kurikulum ?? 'Kurikulum Merdeka') }}</span>
    </div>

    <div style="font-size: 28pt; font-weight: bold; color: {{ $primary }}; text-transform: uppercase; letter-spacing: 1px; line-height: 1.2; margin-top: 6px;">{{ strtoupper($rpp->mata_pelajaran) }}</div>

    <div class="cover-semester">
        Semester {{ $rpp->semester ?? 'Ganjil' }} : Tahun Ajaran {{ $tahunAjaran }}
    </div>

    <div style="border-top: 1px solid #d1d5db; width: 45%; margin: 22px auto;"></div>

    <div class="cover-garuda">
        <img src="{{ $garudaSrc }}" alt="Garuda Pancasila">
    </div>

    <div class="cover-author-label">Disusun oleh:</div>
    <div style="font-size: 15pt; font-weight: bold; color: #1f2937;">{{ $rpp->nama_guru }}</div>
</div>
