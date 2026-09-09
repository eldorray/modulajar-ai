# Deploy ke Hostinger (shared hosting, dengan SSH)

Alur harian setelah semuanya siap:

```bash
# di laptop
npm run build && git add -A && git commit -m "..." && git push origin main

# di server
ssh uXXXXXXXX@<host-hostinger> -p 65002
cd ~/domains/<domain>/public_html/<folder-app>
./deploy-hostinger.sh
```

`public/build` ikut di-commit ke repo, jadi **tidak ada Node/npm di server**.
Konsekuensinya: `npm run build` harus dijalankan di laptop sebelum push, kalau
tidak aset lama yang naik. Skrip memperingatkan bila `public/build/manifest.json`
hilang, tapi tidak bisa tahu apakah isinya basi — itu tanggung jawab kita.

---

## Penyiapan pertama kali

### 1. Subdomain / domain

Buat subdomain di hPanel. Catat folder document root-nya, misalnya
`~/domains/contoh.sch.id/public_html/rpp-ai`. Folder itulah yang akan menjadi
repo git sekaligus document root.

### 2. PHP

Aplikasi butuh **PHP 8.2 atau lebih baru**. Di hPanel → Advanced → PHP
Configuration, pilih versinya, lalu aktifkan ekstensi berikut:

`pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `dom`, `fileinfo`

`gd` dipakai untuk ornamen PDF, `dom` dan `zip` untuk ekspor Word. Kalau ada
yang mati, `deploy-hostinger.sh` memperingatkan saat dijalankan.

### 3. Database

hPanel → Databases → MySQL. Buat database dan user, catat nama database, user,
dan password. Host biasanya `localhost`.

### 4. Clone repo ke document root

Folder document root harus **kosong** dulu (pindahkan/hapus isi lamanya).

```bash
cd ~/domains/<domain>/public_html
rm -rf rpp-ai          # HATI-HATI: pastikan tidak ada data penting di dalamnya
git clone https://github.com/eldorray/modulajar-ai.git rpp-ai
cd rpp-ai
```

Repo privat? Pakai deploy key:

```bash
ssh-keygen -t ed25519 -C "hostinger-deploy" -f ~/.ssh/id_ed25519_deploy -N ""
cat ~/.ssh/id_ed25519_deploy.pub     # tempel ke GitHub → Settings → Deploy keys
printf 'Host github.com\n  IdentityFile ~/.ssh/id_ed25519_deploy\n' >> ~/.ssh/config
git clone git@github.com:eldorray/modulajar-ai.git rpp-ai
```

### 5. Composer

Kalau `composer` belum ada di PATH:

```bash
curl -sS https://getcomposer.org/installer | php -- --install-dir=$HOME --filename=composer.phar
```

Skrip deploy mencari `composer` di PATH, lalu `~/composer.phar`.

### 6. `.env`

```bash
cp .env.example .env
nano .env
```

Yang wajib diubah dari nilai contoh:

```dotenv
APP_ENV=production
APP_DEBUG=false                      # skrip menolak jalan kalau ini true
APP_URL=https://rpp-ai.contoh.sch.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=uXXXXXXXX_rppai
DB_USERNAME=uXXXXXXXX_rppai
DB_PASSWORD=<password database>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
DB_QUEUE_RETRY_AFTER=420             # harus > GenerateRpp::$timeout (360s)

DEEPSEEK_API_KEY=<kunci deepseek>
DEEPSEEK_MODEL=deepseek-chat
```

`.env` tidak dilacak git, jadi `git reset --hard` di dalam skrip deploy tidak
akan menyentuhnya. Cukup diisi sekali.

Lalu:

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

API key DeepSeek juga bisa diisi lewat halaman admin (Pengaturan AI); nilai di
database menang atas `.env`. Nilai di `.env` berguna sebagai cadangan supaya
aplikasi tetap jalan sebelum admin mengisinya.

### 7. Jadikan skrip bisa dijalankan

```bash
chmod +x deploy-hostinger.sh
./deploy-hostinger.sh --dry-run      # lihat langkahnya tanpa mengeksekusi
./deploy-hostinger.sh
```

### 8. Cron

hPanel → Advanced → Cron Jobs. Ganti `<APP>` dengan path absolut aplikasi dan
`<PHP>` dengan path PHP CLI (mis. `/opt/alt/php83/usr/bin/php`).

**Scheduler Laravel** — tiap menit:

```
* * * * * cd <APP> && <PHP> artisan schedule:run >/dev/null 2>&1
```

**Queue worker** — tiap menit, dijaga `flock` agar tidak menumpuk:

```
* * * * * cd <APP> && flock -n storage/framework/worker.lock <PHP> artisan queue:work --stop-when-empty --max-time=300 --tries=1 >>storage/logs/worker.log 2>&1
```

Kenapa begitu:

- Job `GenerateRpp` punya `$timeout = 360` detik. Cron tiap menit tanpa penjaga
  akan menumpuk banyak worker sekaligus.
- `flock -n` melewati cron yang jatuh saat worker sebelumnya masih jalan.
- `--stop-when-empty` membuat worker keluar begitu antrean habis, tidak
  menahan proses sepanjang hari (shared hosting membatasi jumlah proses).
- `--tries=1` cocok dengan `GenerateRpp::$tries = 1`: generasi AI tidak diulang
  otomatis, supaya tidak menghabiskan token dua kali untuk kegagalan yang sama.
- `DB_QUEUE_RETRY_AFTER=420` (> 360) mencegah job yang masih berjalan diambil
  worker lain.

Queue hanya dipakai jalur API v1 (`POST /api/v1/rpps` menjawab 202 lalu klien
polling). Form web membuat RPP secara langsung, jadi web tetap berfungsi
walaupun cron queue belum dipasang.

---

## Keamanan: document root berisi seluruh repo

Karena repo di-clone langsung ke document root, `app/`, `storage/`, dan `.env`
secara fisik berada dalam jangkauan web server. `.htaccess` di root repo
menutupnya dengan **dua lapis**:

1. `<FilesMatch>` dan `<DirectoryMatch>` dengan `Require all denied` — tidak
   membutuhkan mod_rewrite.
2. Aturan rewrite yang mengarahkan sisanya ke `public/`.

Lapisan pertama penting: kalau hanya mengandalkan rewrite, mematikan
mod_rewrite akan membuat seluruh isi repo tersaji, `.env` termasuk.

**Wajib diperiksa setiap kali pindah server.** Semua ini harus menjawab 403
atau 404, tidak boleh 200:

```bash
curl -I https://<domain>/.env
curl -I https://<domain>/composer.json
curl -I https://<domain>/storage/logs/laravel.log
curl -I https://<domain>/app/Models/User.php
curl -I https://<domain>/.git/config
```

Kalau ada yang menjawab 200, hentikan pemakaian sampai beres, lalu **ganti
semua kredensial** di `.env` — anggap sudah bocor.

Lebih aman lagi (butuh symlink, tidak semua panel mau mengikutinya): taruh repo
di `~/apps/rpp-ai` lalu jadikan folder document root symlink ke
`~/apps/rpp-ai/public`. Dengan begitu `.env` mustahil dijangkau lewat URL
apa pun, tanpa bergantung pada aturan `.htaccess`.

---

## Yang dilakukan `deploy-hostinger.sh`

1. Memastikan dijalankan dari root aplikasi (`artisan`, `.git`, `.env` ada).
2. Mencari PHP >= 8.2 dan memperingatkan ekstensi yang mati.
3. Menolak jalan bila `APP_KEY` kosong atau `APP_DEBUG=true`.
4. Menolak jalan bila ada berkas terlacak yang berubah di server (kecuali
   `--force`), supaya suntingan langsung di server tidak hilang tanpa sadar.
5. Menyalakan maintenance mode.
6. `git fetch` + `git reset --hard origin/<branch>`.
7. `composer install --no-dev --optimize-autoloader`.
8. `php artisan migrate --force`.
9. `storage:link` bila belum ada, lalu menyetel izin `storage` dan
   `bootstrap/cache`.
10. Membangun ulang cache config/route/view/event.
11. `queue:restart`, supaya worker lama berhenti dan cron menit berikutnya
    memakai kode baru.
12. Melepas maintenance mode — lewat `trap EXIT`, jadi tetap dilepas walaupun
    ada langkah yang gagal di tengah.

Opsi: `--branch <nama>`, `--force`, `--dry-run`, `--help`.
Variabel lingkungan: `DEPLOY_BRANCH`, `PHP_BIN`, `COMPOSER_BIN`.

---

## Kalau deploy gagal

```bash
tail -50 storage/logs/laravel.log
php artisan up                       # kalau masih terkunci maintenance
git log --oneline -5                 # commit mana yang sedang aktif
```

Kembali ke commit sebelumnya:

```bash
git reset --hard <commit-lama>
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache
```

Migrasi tidak otomatis mundur. Kalau commit yang gagal membawa migrasi yang
sudah jalan, mundurkan dulu dengan `php artisan migrate:rollback --step=1`
sebelum reset — dan pastikan ada backup database.

Ambil backup sebelum deploy yang membawa migrasi berisiko:

```bash
mysqldump -u <user> -p <database> > ~/backup-$(date +%F-%H%M).sql
```
