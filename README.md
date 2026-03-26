# Rankstore

Aplikasi web top up rank Minecraft berbasis Laravel 12 + Livewire + Filament.

Fokus utama aplikasi:

- Toko rank Minecraft dengan katalog, checkout, dan tracking pesanan.
- Pembayaran hybrid: manual transfer + gateway otomatis (Tokopay/AriePulsa).
- Fitur migrasi rank antar akun.
- Notifikasi WhatsApp dan Discord saat order dibuat/diupdate.
- Panel admin Filament untuk manajemen produk, transaksi, dan pengaturan website.

## Ringkasan Teknologi

- Backend: PHP 8.2+, Laravel 12
- Admin panel: Filament 3
- Frontend build: Vite + Tailwind CSS 4
- Database: MySQL/MariaDB
- Notifikasi eksternal: WhatsApp gateway, Discord webhook

## Fitur Utama

- Landing page dinamis (site setting, hero, info server Minecraft).
- Shop rank dengan kategori, pencarian, diskon, dan detail rank.
- Checkout dengan dukungan:
	- Metode manual (upload bukti transfer).
	- Gateway otomatis (redirect pembayaran).
	- Promo code.
	- Upgrade rank (selisih harga).
- Tracking transaksi berdasarkan UUID.
- Migrasi rank (old gamertag -> new gamertag).
- Admin dashboard untuk kelola rank, kategori, promo, order, migrasi, payment method, dan branding website.

## Struktur URL Penting

- Frontend:
	- `/` home.
	- `/shop` daftar rank.
	- `/rank/{rank}` detail rank.
	- `/checkout/{rank}` checkout.
	- `/cek-pembelian` tracking.
	- `/pembelian/{uuid}` detail tracking.
	- `/migrasi` form migrasi.
	- `/detail-migrasi/{uuid}` detail migrasi.
- Admin:
	- `/admin`.
- API callback payment gateway:
	- `POST /api/callback/tokopay`.
	- `POST /api/callback/ariepulsa`.

## Persyaratan Server

Minimum:

- PHP 8.2 atau lebih baru
- Composer 2.x
- Node.js 18+ dan npm (untuk build asset)
- MySQL 8+/MariaDB 10.6+
- Web server Apache/Nginx

Ekstensi PHP yang wajib aktif:

- bcmath
- ctype
- fileinfo
- json
- mbstring
- openssl
- pdo
- pdo_mysql
- tokenizer
- xml
- curl

## Konfigurasi Environment

Salin file environment:

```bash
cp .env.example .env
```

Contoh konfigurasi dasar yang wajib:

```env
APP_NAME="Rankstore"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database

FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

Catatan:

- Kredensial API Tokopay, AriePulsa, WhatsApp, dan Discord dikelola dari menu setting di panel admin (disimpan pada tabel `app_settings`).
- Jika callback gateway dipakai, `APP_URL` wajib URL publik HTTPS yang valid.

## Instalasi Cepat (Lokal / Development)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
npm run dev
php artisan serve
```

Opsional buat admin pertama:

```bash
php artisan make:filament-user
```

## Deploy Production di VPS

Panduan ini cocok untuk Ubuntu + Nginx/Apache.

### 1) Upload source code

Clone dari repository atau upload zip ke server:

```bash
cd /var/www
git clone <repo-url> rankstore
cd rankstore
```

### 2) Install dependency

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

Jika Node.js tidak tersedia di server, build di lokal lalu upload folder `public/build`.

### 3) Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` untuk production (APP_URL, DB, mail, dll).

### 4) Setup database

```bash
php artisan migrate --force
```

### 5) Storage link + permission

```bash
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 6) Optimasi cache Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ada perubahan `.env` atau route, jalankan:

```bash
php artisan optimize:clear
```

### 7) Konfigurasi web server

#### Opsi A: Nginx

Set root ke folder `public`.

Contoh server block:

```nginx
server {
	listen 80;
	server_name domain-anda.com www.domain-anda.com;
	root /var/www/rankstore/public;

	index index.php;

	location / {
		try_files $uri $uri/ /index.php?$query_string;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php8.2-fpm.sock;
	}

	location ~ /\.ht {
		deny all;
	}
}
```

#### Opsi B: Apache

Set `DocumentRoot` ke `.../rankstore/public` dan pastikan `AllowOverride All` aktif.

### 8) SSL

Pasang SSL (Let's Encrypt) dan pastikan callback gateway menggunakan URL HTTPS.

### 9) Buat admin panel

```bash
php artisan make:filament-user
```

## Deploy di Shared Hosting cPanel

Berikut 2 skenario umum.

### Skenario A: cPanel dengan SSH/Terminal aktif

1. Upload source code ke folder di luar `public_html`, contoh: `/home/username/rankstore`.
2. Masuk terminal cPanel, lalu jalankan:

```bash
cd ~/rankstore
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

3. Build asset:
	- Jika Node tersedia di hosting: `npm install && npm run build`.
	- Jika Node tidak tersedia: build di lokal, lalu upload folder `public/build`.

4. Atur domain document root ke folder `~/rankstore/public` (melalui Domains/Addon Domains jika fitur tersedia).

5. Permission:

```bash
chmod -R 775 storage bootstrap/cache
```

6. Jalankan cache:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Skenario B: cPanel tanpa SSH (paling umum)

1. Di komputer lokal, siapkan aplikasi sampai siap upload:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

2. Upload semua file proyek ke folder misalnya `~/rankstore` via File Manager.
3. Pindahkan seluruh isi folder `public` ke `public_html`.
4. Edit file `public_html/index.php` agar path `vendor` dan `bootstrap` mengarah ke folder aplikasi.

Contoh penyesuaian (sesuaikan nama folder user):

```php
require __DIR__.'/../rankstore/vendor/autoload.php';
$app = require_once __DIR__.'/../rankstore/bootstrap/app.php';
```

5. Copy `.env.example` menjadi `.env` di folder aplikasi, lalu isi konfigurasi DB melalui File Manager.
6. Buat database MySQL dari cPanel, lalu isi nilai DB pada `.env`.
7. Jalankan migration dari menu terminal jika tersedia. Jika terminal tidak ada, gunakan metode import SQL manual dari backup/seed yang Anda miliki.
8. Buat symlink storage jika terminal tersedia:

```bash
php artisan storage:link
```

Jika symlink tidak diizinkan shared hosting, gunakan upload file langsung ke `public_html/storage` sebagai alternatif.

9. Set permission folder `storage` dan `bootstrap/cache` ke writable.

Catatan penting skenario tanpa SSH:

- Beberapa provider shared hosting memblokir perintah Artisan tertentu. Jika `php artisan` tidak bisa dijalankan, migrasi harus dilakukan melalui SQL dump/manual.
- Jika symbolic link tidak didukung, pastikan semua aset upload benar-benar ada di folder web-accessible (`public_html/storage`).
- Setelah update kode, ulangi langkah upload file `public/build` agar CSS/JS terbaru ikut terdeploy.

## Konfigurasi Payment Gateway (Wajib untuk Auto Payment)

Di panel admin (`/admin`) isi data berikut pada pengaturan website:

- Tokopay: merchant ID + secret key
- AriePulsa: API key
- WhatsApp gateway: API key + sender number
- Discord: webhook URL

Set URL callback di dashboard provider:

- Tokopay: `https://domain-anda.com/api/callback/tokopay`
- AriePulsa: `https://domain-anda.com/api/callback/ariepulsa`

## Maintenance & Update

Saat pull update kode di production:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm run build
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

- 500 Internal Server Error:
	- Cek `.env`, permission `storage` dan `bootstrap/cache`, lalu lihat log di `storage/logs/laravel.log`.

- Asset CSS/JS tidak tampil:
	- Pastikan folder `public/build` ada dan sudah berisi hasil Vite build.

- Gambar upload tidak muncul:
	- Jalankan `php artisan storage:link` dan pastikan symlink/akses `public/storage` valid.

- Callback pembayaran tidak masuk:
	- Pastikan URL callback benar, HTTPS aktif, dan `APP_URL` sesuai domain publik.

- Tidak bisa login admin:
	- Pastikan user admin sudah dibuat lewat `php artisan make:filament-user`.

## Keamanan Produksi (Checklist)

- `APP_ENV=production`
- `APP_DEBUG=false`
- Gunakan HTTPS
- Jangan commit file `.env`
- Batasi akses database ke host aplikasi
- Backup database terjadwal

## Lisensi

Proyek ini menggunakan lisensi MIT. Lihat file `LICENSE`.
