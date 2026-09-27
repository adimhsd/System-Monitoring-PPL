# Sistem Pemantauan & Penilaian PPL FEB UNIKU 🎓

Sistem Informasi Pemantauan dan Penilaian Praktik Pengenalan Lapangan (PPL) Fakultas Ekonomi dan Bisnis - Universitas Kuningan berbasis Laravel.

Repositori ini adalah **gabungan** dari [SystemMonitoringPPL](https://github.com/adimhsd/SystemMonitoringPPL) (basis aplikasi: logbook, monitoring, luaran, plotting, master data) dan [SystemPenilaianPPL](https://github.com/adimhsd/SystemPenilaianPPL). Menu **Penilaian** Monitoring diganti dengan alur **Input & Rekap Nilai** dari SystemPenilaianPPL.

---

## 🌟 Fitur Utama

- **📊 Dashboard Administrator & Eksekutif**: Ringkasan real-time rekapitulasi nilai PPL, luaran mahasiswa, statistik per kelompok, DPL, dan Mitra.
- **👨‍🎓 Manajemen Data Mahasiswa**: Master data mahasiswa lengkap dengan jenis kelamin, NIM, prodi, fitur eksport & import Excel.
- **👥 Manajemen Akun Kelompok PPL Independen**: Akun kelompok mandiri yang bertanggung jawab bersama tanpa keterikatan akun pribadi ketua.
- **🏢 Master Data Mitra & DPL**: Manajemen instansi mitra magang (SKPD, BUMN, Swasta) dan Dosen Pembimbing Lapangan dilengkapi fitur import & export data Excel.
- **📌 Plotting Kelompok**: Fitur alokasi mahasiswa, DPL, dan Mitra Instansi secara fleksibel.
- **📖 Buku Panduan / Pedoman PPL**: Embed Viewer PDF dokumen pedoman PPL langsung dari Google Drive.
- **📝 Input & Rekap Nilai PPL (dari SystemPenilaianPPL)**: lihat rincian di bawah.
- **📂 Luaran Akhir PPL**: Modul unggah & verifikasi laporan akhir PDF serta link video presentasi YouTube.
- **📅 Approval Logbook Harian (Approve DPL & PIC Mitra)**: Jurnal harian kegiatan mahasiswa dengan verifikasi status *Approved*, foto dokumentasi, dan notifikasi real-time.
- **💾 Backup Database SQL**: Fitur ekspor basis data satu-klik `file_backup_[dd-mm-yyyy].sql` untuk mempermudah migrasi server.

---

## 📝 Input & Rekap Nilai PPL

**Nilai Akhir = (Nilai Mitra × 60%) + (Nilai Laporan DPL × 40%)**. Nilai Akhir dan Huruf Mutu terbentuk otomatis setelah kedua nilai terisi.

| Huruf | A | AB | B | BC | C | CD | D | E |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| Rentang | 81–100 | 75–80,9 | 69–74,9 | 63–68,9 | 57–62,9 | 51–56,9 | 45–50,9 | 0–44,9 |

*Skala dapat diubah Admin melalui tombol **Skala Nilai Huruf**; seluruh nilai langsung dihitung ulang.*

| Role | Hak Akses Penilaian |
| :--- | :--- |
| **PIC Mitra** | Menginput **Nilai Mitra (60%)** + catatan untuk mahasiswa di instansinya (menu *Input Nilai Mitra*). |
| **DPL** | Menginput **Nilai Laporan DPL (40%)** lewat pop-up dengan *live preview* Nilai Akhir & Grade, mengunci nilai (per mahasiswa / massal), dan ekspor rekap Excel mahasiswa bimbingannya. Hanya melihat mahasiswa bimbingannya sendiri. |
| **Admin** | Melihat & mengoreksi seluruh nilai (Mitra & DPL), mengunci dan **membuka kunci** nilai, filter (prodi, kelompok, DPL, status, grade), statistik progres, ekspor rekap Excel berkop FEB, cetak PDF, dan konfigurasi skala huruf. |

**Status nilai:** *Draft* (masih bisa diubah) → *Final (Terkunci)*. Nilai hanya bisa dikunci jika kedua nilai lengkap, dan nilai terkunci hanya dapat dibuka kembali oleh Admin.

## 🎓 Kelompok Rekognisi PPL MBKM

Kelompok yang mitranya berkategori **MBKM** diperlakukan berbeda karena kegiatannya dilaksanakan di luar kampus:
- **Tanpa logbook harian**: menu, tombol, dan halaman logbook tidak tersedia untuk akun kelompok MBKM, serta kelompok MBKM tidak masuk peringatan keterlambatan logbook.
- **Nilai Mitra (60%) diinput langsung oleh DPL** masing-masing bersama Nilai Laporan DPL (40%); PIC Mitra tidak menilai kelompok MBKM.

## 🔐 Ganti Password

Akun **DPL** dan **PIC Mitra** yang masih memakai password default tidak diwajibkan mengganti password saat login pertama; aplikasi hanya menampilkan rekomendasi untuk menggantinya. Role lain tetap diwajibkan.

---

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 13 / PHP 8.3
- **Frontend**: HTML5, Blade, Bootstrap 5.3, Alpine.js, Vanilla CSS & Modern Typography
- **Database**: MySQL / MariaDB / SQLite
- **Libraries**: `maatwebsite/excel` (Import/Export Excel), `barryvdh/laravel-dompdf` (Cetak PDF)

---

## ⚙️ Cara Instalasi & Penggunaan Lokal

### 1. Clone Repositori
```bash
git clone https://github.com/adimhsd/System-Monitoring-PPL.git
cd System-Monitoring-PPL
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

**Instalasi baru dengan data riil PPL 2026/2027** (470 mahasiswa, 92 kelompok termasuk 13 kelompok MBKM, 43 DPL):
```bash
php artisan migrate --seed
php artisan db:seed --class=PplRealDataSeeder
```
`PplRealDataSeeder` memuat backup `data-master/file_backup_[22-08-2026].sql`, lalu menerapkan pembaruan data dari SystemPenilaianPPL (ploting final, Kelompok 79, mutasi mahasiswa, pergantian DPL, dan data MBKM).

**Upgrade database Monitoring yang sudah berjalan:**
```bash
php artisan migrate
```
Migrasi akan (1) mengubah tabel `penilaian_ppl` ke skema baru dengan tetap mempertahankan nilai lama (rata-rata nilai Mitra/DPL lama menjadi Nilai Mitra/Nilai DPL), (2) menambah kategori mitra `MBKM`, dan (3) menerapkan pembaruan data SystemPenilaianPPL secara idempoten. Password akun lama tidak diubah.

**Akun baru yang dibuat oleh sinkronisasi data:**

| Akun | Username | Password awal |
| :--- | :--- | :--- |
| DPL baru (MBKM) | `DPL_PPL42`, `DPL_PPL43` | `FEB_Tangguh` (disarankan diganti) |
| Kelompok 79 | `PPL_Kelompok79` | `password123` |
| 13 Kelompok MBKM | `PPL_Kelompok_MBKM01` s/d `PPL_Kelompok_MBKM13` | `password123` |
| PIC Mitra baru | `pic_virginia_mahakarya_property`, `pic_mbkm` | `password123` (disarankan diganti) |

### 5. Jalankan Application Server
```bash
php artisan serve
```
Akses aplikasi di browser pada alamat `http://127.0.0.1:8000`.

---

## 👨‍💻 Pengembang

© 2026 **Fakultas Ekonomi dan Bisnis - Universitas Kuningan**  
Developed by [**Dosen Sontoloyo**](https://adi-muhamad.my.id/)
