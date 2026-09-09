#!/usr/bin/env bash
# =============================================================================
# Deploy / update aplikasi di shared hosting Hostinger lewat SSH.
#
# Pakai:
#   cd ~/domains/<domain>/public_html/<folder-app>
#   ./deploy-hostinger.sh            # tarik branch main lalu deploy
#   ./deploy-hostinger.sh --branch develop
#   ./deploy-hostinger.sh --force    # buang perubahan lokal di server
#   ./deploy-hostinger.sh --dry-run  # tampilkan langkahnya, jangan jalankan
#
# Skrip ini idempoten: aman dijalankan berulang. Kalau gagal di tengah,
# aplikasi tetap dikeluarkan dari maintenance mode lewat trap EXIT.
#
# public/build ikut di-commit ke git, jadi TIDAK ada npm/node di server.
# Konsekuensinya: jalankan `npm run build` DI LAPTOP sebelum push, kalau tidak
# aset yang lama yang naik. Skrip ini memperingatkan bila manifest hilang.
# =============================================================================
set -euo pipefail

BRANCH="${DEPLOY_BRANCH:-main}"
FORCE=0
DRY_RUN=0

while [ $# -gt 0 ]; do
    case "$1" in
        --branch) BRANCH="${2:?--branch butuh nama branch}"; shift 2 ;;
        --force)  FORCE=1; shift ;;
        --dry-run) DRY_RUN=1; shift ;;
        -h|--help) sed -n '2,20p' "$0" | sed 's/^# \{0,1\}//'; exit 0 ;;
        *) echo "Argumen tidak dikenal: $1" >&2; exit 2 ;;
    esac
done

# --- Utilitas ----------------------------------------------------------------
c_red=$'\033[31m'; c_green=$'\033[32m'; c_yellow=$'\033[33m'; c_dim=$'\033[2m'; c_off=$'\033[0m'
step()  { printf '\n%s==>%s %s\n' "$c_green" "$c_off" "$1"; }
warn()  { printf '%s[peringatan]%s %s\n' "$c_yellow" "$c_off" "$1" >&2; }
fail()  { printf '%s[gagal]%s %s\n' "$c_red" "$c_off" "$1" >&2; exit 1; }
run()   {
    if [ "$DRY_RUN" -eq 1 ]; then
        printf '%s  (dry-run) %s%s\n' "$c_dim" "$*" "$c_off"
    else
        "$@"
    fi
}

# --- Pastikan dijalankan dari root aplikasi ----------------------------------
cd "$(dirname "$0")"
APP_DIR="$(pwd -P)"

[ -f artisan ]       || fail "Tidak ada berkas 'artisan'. Jalankan skrip ini dari root aplikasi Laravel."
[ -d .git ]          || fail "Folder ini bukan repo git. Lakukan 'git clone' dulu (lihat DEPLOY.md)."
[ -f .env ]          || fail "Tidak ada .env. Salin dari .env.example lalu isi kredensialnya (lihat DEPLOY.md)."

# --- Cari PHP CLI yang sesuai -----------------------------------------------
# Hostinger menaruh versi PHP alternatif di /opt/alt/phpXX/usr/bin/php, dan
# 'php' bawaan PATH kadang masih versi lama.
detect_php() {
    if [ -n "${PHP_BIN:-}" ]; then echo "$PHP_BIN"; return; fi
    local candidate
    for candidate in php /opt/alt/php84/usr/bin/php /opt/alt/php83/usr/bin/php /opt/alt/php82/usr/bin/php; do
        if command -v "$candidate" >/dev/null 2>&1; then
            if "$candidate" -r 'exit(PHP_VERSION_ID >= 80200 ? 0 : 1);' 2>/dev/null; then
                echo "$candidate"; return
            fi
        fi
    done
    return 1
}

PHP="$(detect_php)" || fail "Tidak menemukan PHP >= 8.2. Set PHP_BIN=/opt/alt/phpXX/usr/bin/php lalu ulangi."
PHP_VERSION="$("$PHP" -r 'echo PHP_VERSION;')"
step "PHP: $PHP ($PHP_VERSION)"

# Ekstensi yang benar-benar dipakai aplikasi ini.
# Daftar modul diambil sekali ke variabel, lalu dicocokkan dengan pencocokan
# bash — BUKAN lewat `php -m | grep -q`. `grep -q` keluar pada kecocokan
# pertama dan menutup pipe, php kena SIGPIPE, dan `set -o pipefail` membaca itu
# sebagai kegagalan; hasilnya peringatan palsu yang muncul acak.
PHP_MODULES=$'\n'"$("$PHP" -m)"$'\n'
shopt -s nocasematch
for ext in pdo_mysql mbstring openssl gd zip dom fileinfo; do
    [[ $PHP_MODULES == *$'\n'"$ext"$'\n'* ]] \
        || warn "Ekstensi PHP '$ext' tidak aktif. PDF/Word/upload bisa gagal."
done
shopt -u nocasematch

# --- Cari composer -----------------------------------------------------------
detect_composer() {
    if [ -n "${COMPOSER_BIN:-}" ]; then echo "$COMPOSER_BIN"; return; fi
    if command -v composer >/dev/null 2>&1; then echo "composer"; return; fi
    for path in "$HOME/composer.phar" "$APP_DIR/composer.phar" /usr/local/bin/composer; do
        [ -f "$path" ] && { echo "$PHP $path"; return; }
    done
    return 1
}
COMPOSER="$(detect_composer)" || fail "Composer tidak ditemukan. Unduh ke ~/composer.phar (lihat DEPLOY.md)."

# --- Periksa kesehatan .env sebelum menyentuh apa pun -----------------------
env_value() { grep -E "^${1}=" .env | tail -1 | cut -d= -f2- | tr -d '"'"'"' \r'; }

[ -n "$(env_value APP_KEY)" ] || fail "APP_KEY kosong. Jalankan: $PHP artisan key:generate"

APP_ENV_VALUE="$(env_value APP_ENV)"
APP_DEBUG_VALUE="$(env_value APP_DEBUG)"
[ "$APP_ENV_VALUE" = "production" ] || warn "APP_ENV='$APP_ENV_VALUE', bukan 'production'."
case "$APP_DEBUG_VALUE" in
    false|0|"") : ;;
    *) fail "APP_DEBUG='$APP_DEBUG_VALUE'. Wajib false di produksi: halaman error membocorkan konfigurasi dan kredensial." ;;
esac

# --- Aset frontend harus sudah dibangun sebelum push ------------------------
[ -f public/build/manifest.json ] || warn "public/build/manifest.json tidak ada. Jalankan 'npm run build' di laptop lalu commit & push."

# --- Kondisi repo ------------------------------------------------------------
step "Memeriksa kondisi repo"
git rev-parse --verify "refs/remotes/origin/$BRANCH" >/dev/null 2>&1 \
    || git ls-remote --exit-code --heads origin "$BRANCH" >/dev/null 2>&1 \
    || fail "Branch '$BRANCH' tidak ada di remote origin."

DIRTY="$(git status --porcelain --untracked-files=no)"
if [ -n "$DIRTY" ]; then
    printf '%s\n' "$DIRTY" >&2
    if [ "$FORCE" -eq 1 ]; then
        warn "Ada perubahan lokal di server; --force dipakai, perubahan itu akan DIBUANG."
    else
        fail "Ada berkas terlacak yang berubah di server (lihat daftar di atas).
       Perubahan itu akan hilang saat reset. Kalau memang mau dibuang, ulangi dengan --force.
       Kalau berharga, ambil dulu: git diff > ~/perubahan-server.patch"
    fi
fi

# --- Maintenance mode, selalu dilepas lagi ----------------------------------
MAINT_ON=0
cleanup() {
    local code=$?
    if [ "$MAINT_ON" -eq 1 ] && [ "$DRY_RUN" -eq 0 ]; then
        printf '\n'; step "Melepas maintenance mode"
        "$PHP" artisan up || warn "Gagal menjalankan 'artisan up'. Jalankan manual: $PHP artisan up"
    fi
    if [ "$code" -ne 0 ]; then
        printf '\n%s[deploy berhenti dengan galat]%s Aplikasi sudah dikembalikan online. Periksa pesan di atas.\n' "$c_red" "$c_off" >&2
    fi
}
trap cleanup EXIT

step "Menyalakan maintenance mode"
if [ "$DRY_RUN" -eq 0 ]; then
    "$PHP" artisan down --retry=15 >/dev/null 2>&1 && MAINT_ON=1 || warn "Gagal masuk maintenance mode; deploy lanjut tanpa itu."
fi

# --- Tarik kode --------------------------------------------------------------
step "Menarik '$BRANCH' dari origin"
run git fetch origin "$BRANCH" --prune
BEFORE="$(git rev-parse HEAD)"
run git reset --hard "origin/$BRANCH"
AFTER="$(git rev-parse HEAD)"

if [ "$BEFORE" = "$AFTER" ] && [ "$DRY_RUN" -eq 0 ]; then
    printf '   %sSudah pada commit terbaru (%s). Cache tetap dibangun ulang.%s\n' "$c_dim" "${AFTER:0:8}" "$c_off"
else
    printf '   %s -> %s\n' "${BEFORE:0:8}" "${AFTER:0:8}"
fi

# --- Dependency --------------------------------------------------------------
# --no-dev: paket dev tidak boleh ada di produksi. composer.lock ikut di repo,
# jadi versinya persis sama dengan yang diuji di laptop.
step "Memasang dependency PHP (tanpa paket dev)"
run $COMPOSER install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress

# --- Database ----------------------------------------------------------------
step "Menjalankan migrasi"
run "$PHP" artisan migrate --force --no-interaction

# --- Symlink storage ---------------------------------------------------------
if [ ! -e public/storage ]; then
    step "Membuat symlink storage"
    run "$PHP" artisan storage:link
fi

# --- Izin folder tulis -------------------------------------------------------
step "Menyetel izin folder tulis"
run chmod -R ug+rwX storage bootstrap/cache

# --- Cache ------------------------------------------------------------------
# Urutan penting: bersihkan dulu supaya cache lama tidak dipakai saat build.
step "Membangun ulang cache"
run "$PHP" artisan optimize:clear
run "$PHP" artisan config:cache
run "$PHP" artisan route:cache
run "$PHP" artisan view:cache
run "$PHP" artisan event:cache

# --- Queue worker ------------------------------------------------------------
# Worker lama memakai kode versi sebelumnya; suruh berhenti supaya cron
# menit berikutnya menjalankan worker dengan kode baru.
step "Meminta queue worker lama berhenti"
run "$PHP" artisan queue:restart

step "Selesai"
printf '   commit  : %s\n' "$(git rev-parse --short HEAD)"
printf '   pesan   : %s\n' "$(git log -1 --pretty=%s)"
printf '   folder  : %s\n' "$APP_DIR"
printf '\n   %sPeriksa berkas sensitif tidak tersaji (harus 403/404):%s\n' "$c_dim" "$c_off"
printf '   %scurl -I https://<domainmu>/.env%s\n' "$c_dim" "$c_off"
