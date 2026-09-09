<?php

/*
|--------------------------------------------------------------------------
| Desain (Skin) Dokumen RPPM/Modul Ajar
|--------------------------------------------------------------------------
|
| Satu kerangka dokumen, beberapa skin. Skin hanya mengubah tampilan: sampul,
| font, gaya tabel, dan ada-tidaknya ornamen sudut. Urutan serta isi halaman
| tetap sama untuk semua skin.
|
| 'font'    : font-family CSS. Batasi pada font yang dibundel DomPDF
|             (Times, Helvetica, Courier, DejaVu Sans/Serif/Mono). Font sistem
|             yang tak terdaftar diam-diam jatuh ke Times dan skin jadi tak
|             terlihat berbeda.
| 'ornamen' : true = pakai PNG sudut public/decor-{tema}-*.png.
| 'tabel'   : 'solid' = header tabel blok warna tema.
|             'garis' = header putih dengan garis bawah tebal (hemat tinta).
| 'cover'   : nama partial sampul, resources/views/rpp/partials/cover-{cover}.blade.php
|
| Menambah skin = satu entri di sini + satu partial sampul. Key pertama adalah
| default dan dipakai untuk dokumen lama yang kolom `desain`-nya masih NULL.
|
*/

return [
    'klasik' => [
        'label' => 'Klasik',
        'deskripsi' => 'Times New Roman, ornamen sudut, header tabel blok warna.',
        'font' => "'Times New Roman', Times, serif",
        'ornamen' => true,
        'tabel' => 'solid',
        'cover' => 'klasik',
    ],
    'modern' => [
        'label' => 'Modern',
        'deskripsi' => 'Sans-serif, banner warna di sampul, header tabel blok warna.',
        'font' => "'DejaVu Sans', Helvetica, Arial, sans-serif",
        'ornamen' => false,
        'tabel' => 'solid',
        'cover' => 'modern',
    ],
    'minimalis' => [
        'label' => 'Minimalis',
        'deskripsi' => 'Sans-serif, tanpa ornamen, tabel garis tipis, hemat tinta.',
        'font' => "'DejaVu Sans', Helvetica, Arial, sans-serif",
        'ornamen' => false,
        'tabel' => 'garis',
        'cover' => 'minimalis',
    ],
];
