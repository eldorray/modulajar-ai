# Desain Kontrak Mobile API v1

Status: kontrak target; endpoint belum diimplementasikan.

## Tujuan
Menjadi source of truth bersama untuk backend Laravel dan klien Android. Base path adalah `/api/v1`; perubahan breaking wajib memakai versi mayor baru.

## Keputusan arsitektur
- Autentikasi: Laravel Sanctum personal access token per perangkat, dikirim sebagai Bearer token.
- Semua data sensitif dan file hanya tersedia melalui endpoint terautorisasi.
- Respons sukses memakai `data`; koleksi menambahkan `meta` dan `links`.
- Respons gagal memakai `message`, `code`, dan opsional `errors` per field.
- `/auth/me` mengirim role serta capabilities. Klien menyembunyikan menu berdasarkan capability, tetapi backend tetap otoritas final dan fail closed.
- RPP dan STS dibuat asynchronous: create mengembalikan 202 dengan resource `processing`, lalu klien polling endpoint status sampai `completed` atau `failed`.
- `Idempotency-Key` wajib pada pembuatan AI dan penyimpanan koreksi untuk mencegah duplikasi akibat retry.
- Upload menggunakan multipart dengan MIME dan batas ukuran; gambar scan tidak disimpan sebagai base64.
- Pagination mempertahankan bentuk yang sama pada semua koleksi.

## Capability minimum
`dashboard.view`, `rpp.view`, `rpp.create`, `rpp.delete`, `sts.view`, `sts.create`, `sts.delete`, `ljk.view`, `ljk.manage`, `ljk.correct`, `profile.manage`, `settings.view`, `settings.manage`, `admin.users.manage`, `admin.guru.manage`, `admin.rpp.view`. Backend menghitung capabilities; Android tidak menebak dari role.

## Otorisasi
Resource user-scoped hanya boleh diakses pemilik. Admin tidak otomatis dapat mengubah resource milik guru kecuali operation admin khusus menyatakannya. LJK template, answer key, correction, result, dan file scan wajib memverifikasi ownership. Settings memerlukan capability `settings.manage`.

## Error dan retry
401 mencabut sesi lokal; 403 tidak boleh diperlakukan sebagai data kosong; 404 tidak membocorkan keberadaan resource lain; 409 untuk konflik idempotency; 422 untuk validasi; 429 menghormati `Retry-After`; 503 untuk provider AI. Android boleh retry GET dan request ber-idempotency-key dengan backoff, tetapi tidak boleh retry mutation biasa secara buta.

## AI dan schema dinamis
Metadata resource stabil, sedangkan `content_result` RPP/STS bersifat objek terstruktur yang tetap mengizinkan field tambahan agar evolusi prompt tidak mematahkan klien. Android wajib memiliki fallback renderer untuk bagian yang belum dikenali.

## Upload dan download
LJK memakai multipart JPEG/PNG/WebP. Backend memvalidasi MIME nyata, ukuran, ownership, dan menyimpan scan secara privat. PDF/DOCX/XLSX dikirim sebagai binary dengan `Content-Disposition`; semua download membutuhkan token dan policy.

## Definition of done implementasi berikutnya
Kontrak dianggap tersedia hanya setelah route `/api/v1`, Sanctum, API Resources, Form Requests, policies, queue AI, idempotency persistence, contract tests, dan OpenAPI response tests benar-benar lulus.

Spesifikasi mesin: `openapi/api-v1.yaml`.
