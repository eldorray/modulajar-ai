# 🎓 Modul Ajar AI Generator

Aplikasi web untuk membuat **Modul Ajar / RPP** (Rencana Pelaksanaan Pembelajaran) sesuai format **Kurikulum Merdeka Kemdikbud** menggunakan teknologi AI DeepSeek.

![Laravel](https://img.shields.io/badge/Laravel-11-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![AI](https://img.shields.io/badge/AI-DeepSeek-green)

## ✨ Fitur

-   🤖 **AI-Powered Generation** - Modul Ajar dibuat otomatis oleh DeepSeek Chat
-   📄 **Format Kemdikbud** - Output sesuai standar Modul Ajar Kurikulum Merdeka
-   📥 **Export PDF** - Download hasil dalam format PDF profesional
-   🏫 **Pengaturan Sekolah** - Kustomisasi logo, nama sekolah, NSM, NPSN untuk muncul di PDF
-   👥 **Multi-User** - Sistem login dengan role Admin dan User
-   📊 **Dashboard Analytics** - Tracking penggunaan token AI dan estimasi biaya

## 🛠️ Tech Stack

-   **Backend**: Laravel 11, PHP 8.2+
-   **Frontend**: Blade, TailwindCSS, Alpine.js
-   **Database**: MySQL / SQLite
-   **AI Provider**: DeepSeek API
-   **PDF**: DomPDF

## 📦 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/eldorray/modulajar-ai.git
cd modulajar-ai
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=modulajar_ai
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Konfigurasi AI Provider

Tambahkan konfigurasi DeepSeek ke `.env`:

```env
AI_PROVIDER=deepseek
DEEPSEEK_API_KEY=your_deepseek_api_key
```

### 6. Migrasi Database

```bash
php artisan migrate
```

### 7. Seed Data (Opsional)

```bash
php artisan db:seed
```

Ini akan membuat akun admin:

-   **Email**: admin@example.com
-   **Password**: password

### 8. Build Assets

```bash
npm run build
```

### 9. Jalankan Aplikasi

**Development:**

```bash
npm run dev
php artisan serve
```

**Production:**

```bash
npm run build
```

Akses aplikasi di: `http://localhost:8000`

## 📖 Cara Penggunaan

### 1. Login / Register

Buat akun baru atau login dengan akun yang sudah ada.

### 2. Buat Modul Ajar

-   Klik **"Buat RPP Baru"** di sidebar
-   Isi form dengan informasi:
    -   Mata Pelajaran
    -   Fase & Kelas
    -   Topik/Materi
    -   Alokasi Waktu
    -   dll.
-   Klik **"Generate with AI"**
-   Tunggu AI memproses (30-60 detik)

### 3. Download PDF

-   Setelah Modul Ajar selesai, klik **"Download PDF"**
-   PDF akan otomatis terdownload dengan format profesional

### 4. Pengaturan Sekolah (Admin)

-   Akses **"Pengaturan Sekolah"** di sidebar
-   Upload logo sekolah
-   Isi nama sekolah, NSM, NPSN, alamat
-   Data ini akan muncul di PDF yang dihasilkan

## 🔧 Konfigurasi Tambahan

### Storage Link

Untuk menampilkan logo sekolah:

```bash
php artisan storage:link
```

### Cache (Production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📁 Struktur Folder Penting

```
├── app/
│   ├── Http/Controllers/
│   │   ├── RppController.php      # CRUD Modul Ajar
│   │   ├── SettingController.php  # Pengaturan Sekolah
│   │   └── DashboardController.php
│   ├── Models/
│   │   ├── Rpp.php
│   │   └── SchoolSetting.php
│   └── Services/
│       └── DeepSeekService.php    # AI DeepSeek
├── resources/views/
│   ├── rpp/
│   │   ├── create.blade.php       # Form Generate
│   │   ├── show.blade.php         # Detail Modul Ajar
│   │   └── pdf.blade.php          # Template PDF
│   └── settings/
│       └── index.blade.php        # Pengaturan Sekolah
└── public/
    └── logo.png                   # Logo Aplikasi
```

## 💰 Estimasi Biaya AI

| Provider         | Input Token           | Output Token |
| ---------------- | --------------------- | ------------ |
| DeepSeek         | $0.28/1M (cache miss) | $0.42/1M     |

Dashboard menampilkan estimasi biaya dalam Rupiah (kurs: $1 = Rp 16.717).

## 📝 License

MIT License - Silakan gunakan dan modifikasi sesuai kebutuhan.

## 👨‍💻 Author

Developed with ❤️ using AI assistance.
