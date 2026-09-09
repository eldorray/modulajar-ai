{{-- Sampul Modern — banner warna solid, sans-serif, tanpa ornamen.

     Gaya khusus sampul ini ditulis inline, bukan di skin-css.blade.php, supaya
     tiap varian sampul bisa dibaca utuh dalam satu berkas dan CSS bersama tidak
     menumpuk aturan untuk sampul yang sedang tidak dipakai.

     Tinggi blok warna dibentuk dengan padding, bukan `height`: DomPDF sering
     mengabaikan height pada div kosong, sedangkan padding selalu dihormati. --}}
<div class="cover" style="padding: 30px 0 40px;">
    @if(isset($schoolSettings) && $schoolSettings->logo)
    <div style="margin-bottom: 14px;">
        <img src="{{ $isPrint ? asset('storage/' . $schoolSettings->logo) : storage_path('app/public/' . $schoolSettings->logo) }}" alt="Logo" style="max-height: 70px; max-width: 70px;">
    </div>
    @endif

    {{-- Logo dibiarkan di atas banner (latar putih) supaya logo sekolah yang
         berwarna tidak bertabrakan dengan warna tema. --}}
    <div style="background-color: {{ $primary }}; padding: 16px 20px; margin-bottom: 28px;">
        <div style="font-size: 14pt; font-weight: bold; color: #ffffff; text-transform: uppercase; letter-spacing: 2px;">{{ strtoupper($schoolName) }}</div>
        @if($schoolCity)
        <div style="font-size: 9pt; color: #ffffff; letter-spacing: 2px;">{{ strtoupper($schoolCity) }}</div>
        @endif
    </div>

    <div style="padding: 0 20px;">
        <div class="cover-title-main" style="margin-top: 0;">RENCANA PELAKSANAAN<br>PEMBELAJARAN MENDALAM<br><span style="font-size:13pt;">{{ strtoupper($rpp->kurikulum ?? 'Kurikulum Merdeka') }}</span></div>
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

    <div style="background-color: {{ $dark }}; padding: 7px 0; margin-top: 34px;"></div>
</div>
