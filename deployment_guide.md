# 🚀 Panduan Lengkap Deployment VPS & Konfigurasi Cloudflare R2
**Sistem Pemantauan & Penilaian PPL FEB UNIKU**

Panduan ini disusun secara langkah demi langkah (*step-by-step*) khusus untuk Anda yang ingin melakukan *deployment* aplikasi berbasis Laravel 11 ke **VPS (Ubuntu 22.04 / 24.04 LTS)** dan mengintegrasikannya dengan **Cloudflare DNS + Cloudflare R2 Storage** (Object Storage gratis tanpa biaya egress).

---

## 📌 Rekomendasi Arsitektur Deployment

| Komponen | Teknologi Rekomendasi | Alasan & Keunggulan |
| :--- | :--- | :--- |
| **Sistem Operasi** | Ubuntu 22.04 LTS / 24.04 LTS | Sangat stabil, dokumentasi melimpah, dan didukung penuh oleh Laravel. |
| **Web Server** | Nginx | Sangat cepat, hemat RAM, dan mudah dikonfigurasi dengan Cloudflare. |
| **Runtime & DB** | PHP 8.3-FPM + MariaDB / MySQL | Performa maksimal & konsisten dengan lingkungan pengembangan lokal. |
| **Storage Uploads** | Cloudflare R2 (S3-Compatible) | Berkas foto & PDF tersimpan aman di cloud storage (10GB gratis & $0 biaya transfer data/egress). Berkas tidak hilang saat *re-deploy*. |
| **DNS & Security** | Cloudflare Free Plan | Proteksi DDOS, SSL Gratis, caching otomatis, dan manajemen DNS fleksibel. |
| **Process Manager** | Supervisor | Menjalankan `php artisan queue:work` secara otomatis di background server. |

---

## 🛠️ LANGKAH 1: Konfigurasi Cloudflare R2 Storage (Penyimpanan Berkas Foto & PDF)

Cloudflare R2 adalah Object Storage serbaguna yang kompatibel dengan protokol AWS S3.

### 1.1 Membuat Bucket di Cloudflare R2
1. Login ke Dashboard [Cloudflare](https://dash.cloudflare.com/).
2. Pada menu bilah kiri, klik **R2 Object Storage**.
3. Klik tombol **Create Bucket**.
4. Isi **Bucket Name** (misal: `ppl-feb-uniku-storage`).
5. Klik **Create Bucket**.

### 1.2 Membuat API Token Cloudflare R2
1. Di halaman utama **R2**, klik **Manage R2 API Tokens** (di bilah sebelah kanan).
2. Klik tombol **Create API Token**.
3. Pilih hak akses **Edit** (Read & Write permissions).
4. Klik **Create API Token**.
5. Simpan informasi penting berikut (informasi ini hanya ditampilkan sekali):
   - **Access Key ID** (misal: `a1b2c3d4e5f6...`)
   - **Secret Access Key** (misal: `9x8y7z...`)
   - **Endpoint URL** (misal: `https://<ACCOUNT_ID>.r2.cloudflarestorage.com`)

---

## 🖥️ LANGKAH 2: Persiapan Server VPS (Linux Ubuntu)

Login ke VPS Anda via Terminal / PuTTY:
```bash
ssh root@IP_ADDRESS_VPS_ANDA
```

### 2.1 Update System & Install Dependencies
Jalankan perintah berikut untuk memperbarui paket sistem dan menginstall Nginx, PHP 8.3, MariaDB, Composer, Git, dan Node.js:

```bash
# Update paket
apt update && apt upgrade -y

# Software Properties
apt install -y software-properties-common curl git unzip zip supervisor

# Tambah repositori PHP 8.3 (Ondřej Surý)
add-apt-repository ppa:ondrej/php -y
apt update

# Install Nginx, MariaDB, PHP 8.3 & Ekstensi Lengkap
apt install -y nginx mariadb-server php8.3-fpm php8.3-mysql php8.3-mbstring \
php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl php8.3-sqlite3

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install Node.js 20 LTS & NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
```

---

## 🗄️ LANGKAH 3: Pembuatan Database MariaDB/MySQL

```bash
mysql -u root -p
```
*Jika baru pertama kali, tekan Enter.* Jalankan perintah SQL berikut:

```sql
CREATE DATABASE ppl_feb_uniku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ppl_user'@'localhost' IDENTIFIED BY 'PasswordRahasiaProyek123!';
GRANT ALL PRIVILEGES ON ppl_feb_uniku.* TO 'ppl_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 📦 LANGKAH 4: Clone & Deployment Aplikasi ke VPS

### 4.1 Clone Repository
```bash
cd /var/www
git clone https://github.com/adimhsd/SystemMonitoringPPL.git ppl-feb-uniku
cd /var/www/ppl-feb-uniku
```

### 4.2 Install Vendor Dependencies & Build Assets
```bash
# Install paket PHP produksi
composer install --no-dev --optimize-autoloader

# Install paket Node.js & kompilasi asset produk
npm install
npm run build
```

### 4.3 Setup Berkas `.env` Produksi
Salin `.env` dan sesuaikan nilainya:
```bash
cp .env.example .env
nano .env
```

Sesuaikan konfigurasi berikut di dalam berkas `.env`:

```env
APP_NAME="Sistem Monitoring PPL FEB UNIKU"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ppl_feb_uniku
DB_USERNAME=ppl_user
DB_PASSWORD=PasswordRahasiaProyek123!

# Konfigurasi Storage Cloudflare R2
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=AKSES_KEY_ID_R2_ANDA
AWS_SECRET_ACCESS_KEY=SECRET_ACCESS_KEY_R2_ANDA
AWS_DEFAULT_REGION=auto
AWS_BUCKET=ppl-feb-uniku-storage
AWS_ENDPOINT=https://ACCOUNT_ID_ANDA.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
```

Simpan file di nano: Tekan `Ctrl + O`, `Enter`, lalu `Ctrl + X`.

### 4.4 Inisialisasi Laravel Application Key & Migration
```bash
# Generate Key
php artisan key:generate

# Migrasi Database
php artisan migrate --force

# Storage Symlink
php artisan storage:link

# Optimasi Cache Produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4.5 Atur Hak Akses Folder (Permissions)
```bash
chown -R www-data:www-data /var/www/ppl-feb-uniku
chmod -R 775 /var/www/ppl-feb-uniku/storage /var/www/ppl-feb-uniku/bootstrap/cache
```

---

## ⚙️ LANGKAH 5: Konfigurasi Queue Worker (Supervisor)

Agar fitur notifikasi real-time dan email dapat berjalan otomatis di background server:

1. Buat file konfigurasi supervisor:
```bash
nano /etc/supervisor/conf.d/ppl-worker.conf
```

2. Tempelkan isi konfigurasi berikut:
```ini
[program:ppl-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ppl-feb-uniku/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ppl-feb-uniku/storage/logs/worker.log
stopwaitsecs=3600
```

3. Jalankan supervisor:
```bash
supervisorctl reread
supervisorctl update
supervisorctl start ppl-worker:*
```

---

## 🌐 LANGKAH 6: Konfigurasi Nginx Web Server & Cloudflare

### 6.1 Membuat Nginx Server Block
```bash
nano /etc/nginx/sites-available/ppl-feb-uniku
```

Tempelkan kode konfigurasi Nginx berikut (Ganti `domain-anda.com` dengan nama domain Anda):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name domain-anda.com www.domain-anda.com;
    root /var/www/ppl-feb-uniku/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi Nginx:
```bash
ln -s /etc/nginx/sites-available/ppl-feb-uniku /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

### 6.2 Konfigurasi Cloudflare DNS & SSL Certbot
1. Masuk ke Dashboard **Cloudflare** -> **DNS** -> **Records**.
2. Tambahkan **A Record**:
   - **Type**: `A`
   - **Name**: `@` (atau nama subdomain)
   - **IPv4 address**: `IP_ADDRESS_VPS_ANDA`
   - **Proxy status**: `Proxied` (Awan Oranye aktif).
3. Untuk SSL Certificate otomatis via Certbot:
```bash
apt install -y python3-certbot-nginx
certbot --nginx -d domain-anda.com -d www.domain-anda.com
```
4. Di Cloudflare Dashboard -> **SSL/TLS** -> set mode ke **Full (strict)**.

---

## 🎉 SELESAI!
Aplikasi **Sistem Monitoring PPL FEB UNIKU** kini telah beroperasi penuh secara aman, cepat, dan handal di server VPS Anda!
