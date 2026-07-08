<?php

/*
|--------------------------------------------------------------------------
| Tema Warna Dokumen RPPM/Modul Ajar
|--------------------------------------------------------------------------
|
| Dipakai bersama oleh PDF (rpp/pdf.blade.php) dan Word (RppWordExporter)
| serta generator ornamen sudut. Warna hex TANPA '#' (Word butuh tanpa,
| PDF menambahkan '#' sendiri). 'primary' = header tabel & aksen judul,
| 'dark' = gradasi ornamen gelap, 'accent' = warna nama mapel di cover.
|
| Key tema juga dipakai sebagai nama file ornamen: public/decor-{key}-*.png
|
*/

return [
    'merah' => ['label' => 'Merah', 'primary' => 'b91c1c', 'dark' => '7f1d1d', 'accent' => 'facc15'],
    'biru' => ['label' => 'Biru', 'primary' => '1d4ed8', 'dark' => '1e3a8a', 'accent' => 'f59e0b'],
    'hijau' => ['label' => 'Hijau', 'primary' => '047857', 'dark' => '064e3b', 'accent' => 'facc15'],
    'ungu' => ['label' => 'Ungu', 'primary' => '7c3aed', 'dark' => '5b21b6', 'accent' => 'f59e0b'],
    'teal' => ['label' => 'Teal', 'primary' => '0d9488', 'dark' => '115e59', 'accent' => 'f59e0b'],
];
