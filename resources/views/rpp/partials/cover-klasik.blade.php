{{-- Sampul Klasik — salinan persis sampul sebelum fitur skin ada.
     JANGAN diubah tanpa alasan: skin ini yang menjaga dokumen lama tetap sama. --}}
<div class="cover">
    @if(isset($schoolSettings) && $schoolSettings->logo)
    <div class="cover-school-logo">
        <img src="{{ $isPrint ? asset('storage/' . $schoolSettings->logo) : storage_path('app/public/' . $schoolSettings->logo) }}" alt="Logo">
    </div>
    @endif
    <div class="cover-school-name">{{ strtoupper($schoolName) }}</div>
    @if($schoolCity)
    <div class="cover-school-sub">{{ strtoupper($schoolCity) }}</div>
    @endif

    <div class="cover-title-main">RENCANA PELAKSANAAN<br>PEMBELAJARAN MENDALAM<br><span style="font-size:13pt;">{{ strtoupper($rpp->kurikulum ?? 'Kurikulum Merdeka') }}</span></div>
    <div class="cover-subject">{{ strtoupper($rpp->mata_pelajaran) }}</div>
    <div class="cover-semester">
        Semester {{ $rpp->semester ?? 'Ganjil' }} : Tahun Ajaran {{ $tahunAjaran }}
    </div>

    <div class="cover-garuda">
        <img src="{{ $garudaSrc }}" alt="Garuda Pancasila">
    </div>

    <div class="cover-author-label">Disusun oleh:</div>
    <div class="cover-author-name">{{ $rpp->nama_guru }}</div>
</div>
