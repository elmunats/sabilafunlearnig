# Panduan Lengkap Deployment ke Hosting — Sabila Fun Learning

Tutorial ini menjelaskan langkah demi langkah cara meng-online-kan aplikasi **Sabila Fun Learning** (Laravel 11 + Inertia.js Vue 3 + Tailwind CSS + MySQL/MariaDB) ke **Shared Hosting (DirectAdmin / cPanel)** maupun **VPS**.

---

## 1. Spesifikasi Server Minimum

Pastikan layanan hosting Anda memenuhi syarat berikut:
- **PHP**: Versi `8.2` atau `8.1+`
- **Ekstensi PHP**: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`
- **Database**: MySQL `8.0+` atau MariaDB `10.4+`
- **Web Server**: Apache / LiteSpeed (dengan `mod_rewrite` aktif) atau Nginx

---

## 2. Tahap 1: Persiapan & Build di Komputer Lokal

Sebelum mengunggah file ke hosting, kompilasi aset frontend (Vue 3 & Tailwind CSS) di komputer lokal agar server hosting tidak perlu menjalankan `npm run build`:

```bash
# 1. Pastikan dependensi terpasang
composer install --optimize-autoloader --no-dev
npm install

# 2. Build aset produksi ke dalam folder public/build
npm run build

# 3. Bersihkan cache lokal sebelum membuat arsip ZIP
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Setelah `npm run build` selesai, pastikan folder `public/build/` dan file `public/logo-sabiladunlearning.png` sudah ada.

Buat arsip **`sabila-app.zip`** yang berisi seluruh file proyek (kecuali folder `node_modules` dan `.git`):

```bash
zip -r sabila-app.zip . -x "node_modules/*" ".git/*" "tests/*" "storage/logs/*.log"
```

> **Catatan:** Jika Anda ingin menjalankan `composer install` di server via SSH, Anda juga dapat mengecualikan folder `vendor/*` lalu menjalankan `composer install --no-dev --optimize-autoloader` di server. Namun pada Shared Hosting tanpa SSH, sertakan folder `vendor/` di dalam file ZIP.

---

## 3. Tahap 2: Membuat Database MySQL di DirectAdmin / cPanel

1. Masuk ke panel **DirectAdmin** atau **cPanel**.
2. Buka menu **MySQL Management** / **MySQL Databases**.
3. Klik **Create New Database** dan catat informasi berikut:
   - **Database Name**: contoh `u12345_sabila`
   - **Database Username**: contoh `u12345_sabilauser`
   - **Database Password**: contoh `SandiKu@t2026!`
   - **Database Host**: `localhost` (atau `127.0.0.1`)

---

## 4. Tahap 3: Upload & Struktur Folder Aman di Shared Hosting

Agar file inti Laravel (`.env`, `app/`, `database/`, `storage/`) tidak dapat diakses publik dari luar, pisahkan folder inti Laravel dengan folder `public_html`.

### Struktur Direktori yang Direkomendasikan di DirectAdmin / cPanel:

```text
/home/username/
├── sabila_core/                  <-- Ekstrak seluruh isi sabila-app.zip di sini
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── .env
└── domains/namadomain.com/
    └── public_html/              <-- Salin seluruh isi folder public/ ke sini
        ├── build/
        ├── index.php
        ├── .htaccess
        ├── logo-sabiladunlearning.png
        ├── favicon.ico
        ├── apple-touch-icon.png
        ├── manifest.webmanifest
        └── sw.js
```

### Langkah-langkah di File Manager Hosting:
1. Buat folder baru bernama **`sabila_core`** di direktori home (`/home/username/sabila_core`).
2. Unggah dan ekstrak **`sabila-app.zip`** ke dalam `/home/username/sabila_core`.
3. Pindahkan atau salin seluruh isi dari `/home/username/sabila_core/public/` ke dalam folder **`public_html`** domain Anda.
4. Edit file **`public_html/index.php`** agar mengarah ke folder `sabila_core`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Sesuaikan path relatif dari public_html ke folder sabila_core
// Jika public_html berada di /home/username/domains/domain.com/public_html (DirectAdmin), gunakan '/../../../sabila_core'
// Jika public_html berada di /home/username/public_html (cPanel), gunakan '/../sabila_core'
$corePath = __DIR__ . '/../sabila_core'; // Sesuaikan dengan letak folder sabila_core Anda

if (file_exists($maintenance = $corePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $corePath . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $corePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

> **Alternatif (Jika bisa mengubah Document Root di DirectAdmin/Subdomain):**
> Jika Anda menggunakan Subdomain atau VPS dan dapat mengatur **Document Root** langsung ke `/home/username/sabila_core/public`, Anda **tidak perlu** memindahkan isi folder `public/` maupun mengubah `index.php`.

---

## 5. Tahap 4: Konfigurasi File `.env` Produksi

Salin `.env.example` menjadi `.env` di dalam folder `sabila_core/.env`, lalu sesuaikan nilainya seperti berikut:

```dotenv
APP_NAME="Sabila Fun Learning"
APP_ENV=production
APP_KEY=base64:ISI_DENGAN_APP_KEY_ANDA
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://domain-anda.com

APP_LOCALE=id
APP_FALLBACK_LOCALE=id

LOG_CHANNEL=stack
LOG_LEVEL=error

# Konfigurasi Database MySQL / MariaDB
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u12345_sabila
DB_USERNAME=u12345_sabilauser
DB_PASSWORD="SandiKu@t2026!"

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_STORE=database
QUEUE_CONNECTION=database

# Penyimpanan Foto Privat (Pilih 'local' untuk disk privat server, atau 's3' untuk Cloudflare R2 / AWS S3)
FILESYSTEM_DISK=local
PRIVATE_MEDIA_DISK=private

# Opsional: Jika menggunakan Cloudflare R2 / AWS S3 untuk penyimpanan foto
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=sabila-private-media
AWS_ENDPOINT=https://<account_id>.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

## 6. Tahap 5: Migrasi Database & Permission Folder

### A. Jika Memiliki Akses Terminal / SSH di Hosting:

Jalankan perintah berikut dari dalam folder `sabila_core`:

```bash
cd ~/sabila_core

# 1. Setel permission folder storage dan bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 2. Generate APP_KEY (jika belum terisi)
php artisan key:generate --force

# 3. Jalankan migrasi tabel dan seeder data awal (termasuk akun Admin/Guru/Orang Tua & aset SVG)
php artisan migrate --seed --force

# 4. Optimasi cache untuk produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### B. Jika Menggunakan Shared Hosting Tanpa Akses SSH:

1. **Ekspor Database dari Lokal ke phpMyAdmin**:
   - Di komputer lokal, ubah sementara `.env` ke MySQL lokal, jalankan `php artisan migrate:fresh --seed`, lalu ekspor database menjadi file `sabila_db.sql`.
   - Buka **phpMyAdmin** di DirectAdmin/cPanel, pilih database `u12345_sabila`, lalu klik **Import** dan unggah `sabila_db.sql`.
2. **Pastikan Folder Storage Ikut Terunggah**:
   - Pastikan folder `storage/app/private/avatars`, `storage/app/private/seed`, dan `storage/app/private/proofs` dari lokal ikut terunggah ke `sabila_core/storage/app/private/`.
3. **Setel Permission Folder di File Manager**:
   - Klik kanan pada folder `storage` dan `bootstrap/cache`, lalu atur permission ke **`775`** (atau `755` sesuai kebijakan suEXEC hosting Anda).

---

## 7. Tahap 6: Konfigurasi `.htaccess` & SSL (HTTPS)

Pastikan file `public_html/.htaccess` memiliki konfigurasi standar Laravel berikut agar routing Inertia.js berjalan lancar:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Paksa HTTPS (Opsional namun sangat disarankan untuk PWA)
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 8. Checklist Pasca-Deployment

1. Buka `https://domain-anda.com/login` — pastikan **Logo Sabila Fun Learning** dan **Favicon** tampil dengan benar.
2. Masuk menggunakan akun Admin (`admin@sabilafun.id` / `password123` — lihat [`DEMO.md`](./DEMO.md)), lalu segera ubah kata sandi atau tambahkan akun riil untuk penggunaan operasional.
3. Uji unggah foto kegiatan di menu **Foto Kegiatan** untuk memastikan *Temporary Signed URL* (`/private-media/...`) berfungsi dengan baik.
4. Uji pemasangan PWA di ponsel (**Add to Home Screen** di Chrome Android / Safari iOS).

