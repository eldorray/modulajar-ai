# Desain Dokumen (Skin) untuk Unduhan RPP

Tanggal: 2026-09-09
Status: disetujui, siap diimplementasikan

## Masalah

Guru hanya bisa mengganti **palet warna** dokumen (`rpps.tema`, lima pilihan di
`config/rpp_themes.php`). Tata letak, tipografi, bentuk tabel, dan ornamen sudut
identik untuk semua dokumen, sehingga setiap Modul Ajar yang dicetak terlihat sama.
Yang diminta: beberapa **desain** dokumen, dengan warna sebagai salah satu variabel
di dalamnya.

## Keputusan

Empat keputusan yang menjadi batas pekerjaan ini:

1. **Skin, bukan template terpisah.** Satu kerangka dokumen; desain mengubah sampul,
   font, gaya tabel, dan ada-tidaknya ornamen. Urutan dan isi halaman tetap sama.
   Alasan: setiap template baru berarti ~1.000 baris Blade yang harus dirawat sejajar
   dan pasti melenceng satu sama lain.
2. **Desain dipilih di dua titik.** Tersimpan di dokumen sebagai default
   (`rpps.desain`), dan masih bisa ditimpa saat mengunduh tanpa memanggil AI lagi.
3. **Cakupan format: PDF dan Cetak.** Keduanya memakai Blade yang sama, jadi sekali
   kerja. `RppWordExporter` tidak disentuh dan tetap mengikuti warna saja.
4. **Nol aset baru.** Skin baru hanya memakai blok warna solid, garis, dan shading —
   semuanya dirender DomPDF dengan andal. Tidak ada PNG ornamen baru.

## Skin versi pertama

| Key | Font | Ornamen | Tabel | Sampul |
| --- | --- | --- | --- | --- |
| `klasik` | Times New Roman | PNG sudut (yang sekarang) | header blok warna | rata tengah, seperti sekarang |
| `modern` | DejaVu Sans | tidak ada | header blok warna | banner warna solid di atas |
| `minimalis` | DejaVu Sans | tidak ada | header putih, garis bawah tebal | polos, garis rambut |

`klasik` adalah default dan **wajib menghasilkan keluaran yang identik dengan hari
ini**. Dokumen lama (yang `desain`-nya `NULL`) jatuh ke `klasik`, jadi tidak ada
dokumen yang berubah tampilan akibat perubahan ini.

DejaVu Sans dipilih karena dibundel DomPDF dan lengkap glifnya; font sistem yang
tidak terdaftar akan diam-diam jatuh ke Times dan membuat skin tak terlihat berbeda.

## Kendala DomPDF yang membentuk desain ini

Tercatat karena melanggarnya menghasilkan halaman yang rusak tanpa pesan error:

- SVG, gradient CSS, dan segitiga border tidak dirender. Ornamen yang ada berupa PNG.
- `margin: 0` pada `html` menggeser acuan elemen `position: fixed`, melempar ornamen
  ke luar kanvas. Reset CSS harus tetap bertarget, jangan `* {}`.
- `height` pada `.cover` diabaikan bersama `box-sizing`, mendorong sampul ke halaman 2.
- Warna latar solid, border, dan shading tabel aman.

## Arsitektur

### Konfigurasi

`config/rpp_designs.php` — tiap skin adalah data, bukan kode:

```php
'klasik' => [
    'label' => 'Klasik',
    'deskripsi' => '...',
    'font' => "'Times New Roman', Times, serif",
    'ornamen' => true,
    'tabel' => 'solid',   // 'solid' | 'garis'
    'cover' => 'klasik',  // nama partial sampul
],
```

Menambah skin keempat = satu entri di sini + satu partial sampul.

### Resolusi nilai (batas kepercayaan)

`App\Support\RppDocumentStyle` dengan dua metode statis:

```php
RppDocumentStyle::design(?string $requested, ?string $stored): string
RppDocumentStyle::theme(?string $requested, ?string $stored): string
```

Keduanya memakai daftar putih key config: `requested` dipakai hanya jika ada di
config, kalau tidak jatuh ke `stored`, lalu ke default. Query param tidak pernah
masuk ke `config()` atau nama view tanpa melewati sini — ini yang mencegah nilai
karangan dari URL menjadi path include.

### Partial Blade

Baru, di `resources/views/rpp/partials/`:

- `skin-css.blade.php` — CSS dasar yang selama ini dipakai bersama, kini
  diparametrikan oleh `$design` dan palet warna.
- `cover.blade.php` — ornamen (kalau `$design['ornamen']`), nomor halaman, lalu
  menyertakan partial sampul sesuai `$design['cover']`.
- `cover-klasik.blade.php`, `cover-modern.blade.php`, `cover-minimalis.blade.php`.

Markup sampul di `pdf.blade.php` dan `pdf_deep_learning.blade.php` saat ini **identik
byte per byte**, jadi satu partial melayani keduanya. `cover-klasik` adalah salinan
persis markup itu.

Blok `<style>` kedua template sama kecuali kelas khusus masing-masing. Karena itu
kerangka tetap dua berkas: tiap template memanggil `@include('rpp.partials.skin-css')`
lalu melanjutkan dengan kelas khususnya sendiri.

- Khusus `pdf.blade.php`: `.tbl-langkah .fase-sintaks`, `.tbl-langkah .durasi`
- Khusus `pdf_deep_learning.blade.php`: `.subsection-num`, `.daftar-isi .level-3`,
  `.tbl-langkah .phase-header`, `.sub-section`, `.kse-tag`, `.activity-list`, blok
  Asesmen, `.materi-sub`

Menggabungkan dua kerangka itu menjadi satu **bukan** bagian dari pekerjaan ini:
membongkar 2.100 baris template produksi demi kerapian, bukan demi fitur yang diminta.

### Bahaya penamaan

`pdf_deep_learning.blade.php` sudah memakai `$desain` untuk
`$content['desain_pembelajaran']`. Variabel skin **harus** bernama `$designKey` dan
`$design`. Memakai `$desain` akan menimpa data pembelajaran dan merusak template itu
tanpa error yang jelas.

### Aliran data

```
Form Buat RPP  --(radio desain)-->  validasi in:keys  -->  rpps.desain
                                                              |
Detail RPP  --(dropdown desain+warna)--> ?desain=&tema=  -->  RppDocumentStyle
                                                              |
                                            rpp.pdf / rpp.print --> Blade --> DomPDF
```

Controller meneruskan `$designKey` dan `$themeKey` yang sudah aman ke view. Blok
`@php` di puncak tiap template tetap menghitung palet, dengan tambahan:

```php
$designKey = $designKey ?? $rpp->desain ?? 'klasik';
$design = config('rpp_designs.'.$designKey) ?? config('rpp_designs.klasik');
```

Fallback di dalam template dipertahankan supaya view tetap benar bila dirender
langsung (mis. dari test) tanpa melewati controller.

Tidak ada panggilan AI di jalur ini. Mengganti desain hanya me-render ulang dokumen
dari `content_result` yang sudah ada — nol token.

### Antarmuka

- **Form Buat RPP** — radio Desain di samping radio Tema. Pratinjau sampul live yang
  sudah ada ikut berganti skin (tiga varian sampul di dalam pratinjau).
- **Detail RPP** — tombol PDF dan Cetak menjadi dropdown berisi pilihan Desain dan
  Warna, default dari nilai tersimpan dokumen, lalu menyusun query param.
- **Daftar RPP** — tombol PDF dibiarkan; memakai nilai tersimpan dokumen.

## Basis data

Satu migration: `rpps.desain` — `string`, `nullable`, default `NULL`.

`NULL` sengaja dipilih sebagai default alih-alih `'klasik'`: nilai itu berarti
"dokumen ini dibuat sebelum ada pilihan desain", dan resolusi di satu tempat sudah
memetakannya ke `klasik`. Menulis `'klasik'` ke ratusan baris lama tidak memberi
manfaat apa pun.

Tambahkan `desain` ke `$fillable` model `Rpp`.

## Penanganan galat

| Kondisi | Perilaku |
| --- | --- |
| `?desain=apapun` yang tidak dikenal | jatuh ke desain dokumen, lalu `klasik`. Tanpa error. |
| `?tema=` tidak dikenal | jatuh ke tema dokumen, lalu `merah`. |
| `rpps.desain` `NULL` (dokumen lama) | `klasik` |
| Key config terhapus setelah dokumen dibuat | jatuh ke `klasik` |
| Otorisasi | tidak berubah: bukan pemilik dan bukan admin tetap 403 |

Prinsipnya: pilihan desain tidak pernah menjadi jalur gagal. Dokumen selalu bisa
diunduh; yang paling buruk terjadi hanyalah tampil dalam desain default.

## Rencana uji

Berkas: `tests/Feature/RppDocumentDesignTest.php`

1. Tiap desain × dua kurikulum (biasa dan Deep Learning) merender print view dengan
   status 200 dan memuat teks sampul.
2. `klasik` tidak berubah: memuat `Times New Roman` dan `decor-merah-tr.png`.
3. `modern` dan `minimalis` tidak memuat satu pun `decor-*.png`, dan memakai DejaVu Sans.
4. `?desain=` ngawur jatuh ke desain dokumen, bukan error, dan nilainya tidak muncul
   sebagai nama partial.
5. Dokumen lama (`desain` `NULL`) merender `klasik`.
6. Unduh PDF satu kali sebagai uji asap: 200 dan `content-type: application/pdf`.
7. `store()` menyimpan `desain` yang valid dan menolak yang tidak ada di config.
8. Otorisasi: user lain tetap 403 walau membawa query param.

Uji unit `RppDocumentStyle` tercakup lewat kasus 4, 5, dan 7; tidak perlu berkas
terpisah.

## Verifikasi keluaran `klasik`

Sebelum template disentuh, sepuluh render (dua kurikulum × lima warna) disimpan
sebagai snapshot. Setelah seluruh refactor selesai, kesepuluhnya dirender ulang dan
dibandingkan: **himpunan aturan CSS identik** (64 aturan untuk Modul Ajar, 77 untuk
Deep Learning) dan **markup di luar `<style>` identik**.

Berkas mentahnya tidak sama persis, dan ini disengaja:

- `@page` kehilangan indentasi karena kini datang dari partial
- satu baris kosong bergeser di sekitar komentar Blade
- dua aturan khusus tiap template pindah ke akhir blok `<style>`

Ketiganya tidak mengubah CSS yang terbaca DomPDF. Pemindahan aturan aman karena tidak
ada aturan lain yang menyetel properti sama pada selektor yang sama, jadi urutan tidak
menentukan hasil.

Satu bug tertangkap oleh snapshot ini dan tidak akan terlihat tanpa perbandingan
tersebut: `{{ $design['font'] }}` di-escape Blade menjadi `&#039;Times New Roman&#039;`,
sehingga deklarasi font batal tanpa error dan seluruh dokumen diam-diam memakai font
bawaan. Perbaikannya `{!! !!}`, aman karena nilainya berasal dari config.

## Di luar cakupan

- Menggabungkan `pdf.blade.php` dan `pdf_deep_learning.blade.php`
- Skin untuk Word (`RppWordExporter` tidak disentuh)
- Ornamen bergambar baru dan generator PNG-nya
- Desain buatan pengguna atau pengunggahan warna sendiri
