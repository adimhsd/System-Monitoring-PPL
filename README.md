# Sistem Pemantauan & Penilaian PPL FEB UNIKU 🎓

Sistem Informasi Pemantauan dan Penilaian Praktik Pengenalan Lapangan (PPL) Fakultas Ekonomi dan Bisnis - Universitas Kuningan berbasis Laravel.

---

## 🌟 Fitur Utama

- **📊 Dashboard Administrator & Eksekutif**: Ringkasan real-time rekapitulasi nilai PPL, luaran mahasiswa, statistik per kelompok, DPL, dan Mitra.
- **👨‍🎓 Manajemen Data Mahasiswa**: Master data mahasiswa lengkap dengan jenis kelamin, NIM, prodi, fitur eksport & import Excel.
- **👥 Manajemen Akun Kelompok PPL Independen**: Akun kelompok mandiri yang bertanggung jawab bersama tanpa keterikatan akun pribadi ketua.
- **🏢 Master Data Mitra & DPL**: Manajemen instansi mitra magang (SKPD, BUMN, Swasta) dan Dosen Pembimbing Lapangan dilengkapi fitur import & export data Excel.
- **📌 Plotting Kelompok**: Fitur alokasi mahasiswa, DPL, dan Mitra Instansi secara fleksibel.
- **📖 Buku Panduan / Pedoman PPL**: Embed Viewer PDF dokumen pedoman PPL langsung dari Google Drive.
- **📝 Penilaian Individual Mahasiswa (60% Mitra + 40% DPL)**: Evaluasi individual kriteria Kedisiplinan, Etika, Kerjasama, dan Kualitas Kerja dengan rekapitulasi nilai otomatis (A, B, C, D, E) & pencetakan PDF Nilai PPL.
- **📂 Luaran Akhir PPL**: Modul unggah & verifikasi laporan akhir PDF serta link video presentasi YouTube.
- **📅 Approval Logbook Harian (Approve DPL & PIC Mitra)**: Jurnal harian kegiatan mahasiswa dengan verifikasi status *Approved*, foto dokumentasi, dan notifikasi real-time.
- **💾 Backup Database SQL**: Fitur ekspor basis data satu-klik `file_backup_[dd-mm-yyyy].sql` untuk mempermudah migrasi server.

---

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 11 / PHP 8.3
- **Frontend**: HTML5, Blade, Bootstrap 5.3, Alpine.js, Vanilla CSS & Modern Typography
- **Database**: MySQL / MariaDB / SQLite
- **Libraries**: `maatwebsite/excel` (Import/Export Excel), `barryvdh/laravel-dompdf` (Cetak PDF)

---

## ⚙️ Cara Instalasi & Penggunaan Lokal

### 1. Clone Repositori
```bash
git clone https://github.com/adimhsd/SystemMonitoringPPL.git
cd SystemMonitoringPPL
```

### 2. Instal Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env` dan atur kredensial database Anda:
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Migrasi & Seeder Database
```bash
php artisan migrate --seed
```

### 5. Jalankan Application Server
```bash
php artisan serve
```
Akses aplikasi di browser pada alamat `http://127.0.0.1:8000`.

---

## 👨‍💻 Pengembang

© 2026 **Fakultas Ekonomi dan Bisnis - Universitas Kuningan**  
Developed by [**Dosen Sontoloyo**](https://adi-muhamad.my.id/)
