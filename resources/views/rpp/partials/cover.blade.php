{{-- Sampul dokumen. Dipakai bersama oleh rpp/pdf.blade.php dan
     rpp/pdf_deep_learning.blade.php — markup sampul kedua template identik.

     Ornamen sudut hanya dipasang untuk skin yang memakainya. Elemen fixed
     WAJIB berada langsung di bawah <body>: DomPDF tidak merender position:fixed
     yang berada di dalam parent position:relative. --}}
@if ($design['ornamen'])
{{-- Ornamen sudut: fixed langsung di bawah <body> (DomPDF tak merender fixed
     di dalam parent position:relative). Berulang otomatis di semua halaman. --}}
<img class="fx-dots" src="{{ $isPrint ? asset("decor-{$themeKey}-dots.png") : public_path("decor-{$themeKey}-dots.png") }}" alt="">
<img class="fx-tr" src="{{ $isPrint ? asset("decor-{$themeKey}-tr.png") : public_path("decor-{$themeKey}-tr.png") }}" alt="">
<img class="fx-bl" src="{{ $isPrint ? asset("decor-{$themeKey}-bl.png") : public_path("decor-{$themeKey}-bl.png") }}" alt="">
<img class="fx-br" src="{{ $isPrint ? asset("decor-{$themeKey}-br.png") : public_path("decor-{$themeKey}-br.png") }}" alt="">
<div class="page-num"></div>
@else
<div class="page-num"></div>
@endif

@include('rpp.partials.cover-'.$design['cover'])
