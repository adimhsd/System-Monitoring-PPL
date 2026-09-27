# Product Requirement Document (PRD) — Revisi v1.0
# Sistem Monitoring Praktik Pengenalan Lapangan (PPL) — Fakultas Ekonomi dan Bisnis

---

## 1. Overview & Business Objectives

Sistem Monitoring PPL FEB adalah aplikasi web dengan kapabilitas **Progressive Web App (PWA)**, responsif dan ringan, untuk memfasilitasi pelaporan aktivitas magang selama 1 bulan (4 minggu) di instansi pemerintah (SKPD) maupun swasta/UMKM.

| Parameter | Ketentuan |
|---|---|
| Fakultas | Ekonomi dan Bisnis (Manajemen, Akuntansi, Bisnis Digital) |
| Durasi Magang | 1 bulan (4 minggu / ±20 hari kerja) |
| Ukuran Kelompok | 2–10 mahasiswa/kelompok, lintas prodi. **Hanya Ketua Kelompok yang punya akun.** |
| Beban DPL | 1 DPL membimbing 2–4 kelompok, **total mahasiswa bimbingan ≤ 10** |
| Bobot Penilaian | 60% Pembimbing Mitra (performa lapangan) + 40% DPL (Laporan Akhir + Video) |

**Catatan**: karena hanya ketua yang login, sistem tidak mencatat kehadiran per anggota secara digital — hal ini sudah tertangani secara operasional oleh PIC Mitra yang memantau langsung di lapangan, sehingga tidak perlu modul presensi terpisah di sistem.

---

## 2. Tech Stack

| Layer | Pilihan | Catatan Arsitektur |
|---|---|---|
| Arsitektur | Monolithic (Laravel) | Sesuai untuk skala fakultas (ratusan mahasiswa/semester), hindari over-engineering ke microservices |
| Backend | Laravel 11.x (PHP 8.2+) | — |
| Frontend | Blade + Bootstrap 5.3 + Alpine.js | Mobile-first |
| Database | MySQL 8.x / MariaDB | Wajib aktifkan `strict mode` |
| File Storage | Cloudflare R2 (S3-compatible, **private bucket**) | Lihat §7 untuk strategi akses aman |
| Auth | Laravel Session-based + Middleware RBAC | Tambahkan rate limiting (lihat §7) |
| Queue/Job | **Database queue driver** + Cron (`* * * * * php artisan schedule:run`) | Shared hosting umumnya tidak punya `supervisor`; gunakan cron cPanel untuk memicu `queue:work --stop-when-empty` setiap menit |
| Export | `maatwebsite/excel` (Excel), `barryvdh/laravel-dompdf` (PDF) | — |
| Hosting | cPanel Shared Hosting + Cloudflare Proxy/SSL | Lihat batasan di §8 (NFR) |
| Monitoring/Log | Laravel log daily channel + **Sentry (free tier)** untuk error tracking | Jangan aktifkan Telescope di production (beban resource shared hosting) |

**Perubahan kunci vs v1**: penambahan strategi queue eksplisit (kompresi gambar & generate PDF rekap tidak boleh blocking request di shared hosting yang resource-nya terbatas), dan bucket R2 diset **private** dengan signed URL, bukan public.

---

## 3. User Roles & Permission Matrix

| Role | Hak Akses Utama |
|---|---|
| **Admin (Fakultas/Unit PPL)** | Master data mitra/prodi/user, plotting kelompok & DPL, monitoring keterlambatan logbook, ekspor rekap nilai |
| **Ketua Kelompok (Mahasiswa)** | Input logbook harian, upload luaran akhir |
| **PIC/Pembimbing Mitra** | Tandai "Sudah Dilihat" logbook harian, isi nilai lapangan (60%) |
| **DPL** | Tandai "Sudah Dilihat" logbook harian, catatan supervisi, nilai laporan & video (40%) |

**Tambahan**: Admin perlu dashboard khusus untuk memantau kelompok yang **belum mengisi logbook >24 jam** dan kelompok yang **belum ditandai "sudah dilihat"** oleh mitra/DPL >3 hari — ini krusial secara operasional tapi tidak ada di v1.

---

## 4. Functional Requirements

### A. Autentikasi & Profil
- Login multi-role dengan **rate limiting** (maks 5 percobaan/menit per IP, lockout 15 menit) untuk cegah brute force.
- Reset password via token email (bukan self-reset tanpa verifikasi).
- Wajib ganti password default saat login pertama.

### B. Manajemen Kelompok & Plotting (Admin)
- Penempatan mahasiswa ke kelompok, penetapan mitra.
- Alokasi DPL ke kelompok dilakukan **manual oleh tim Admin** saat proses plotting (biasanya disiapkan dulu di luar sistem, mis. spreadsheet, sebelum diinput). Sistem **tidak** melakukan validasi blocking otomatis, cukup menampilkan **info total mahasiswa bimbingan per DPL** (read-only) di halaman plotting sebagai referensi agar Admin mudah memastikan kuota ≤ 10 saat input.
- Registrasi akun PIC Mitra terhubung ke mitra terkait.
- Fitur pindah DPL/mitra/ketua di tengah masa PPL (untuk kasus DPL berhalangan, ketua mengundurkan diri, atau mitra bermasalah) — cukup update data, tanpa perlu log audit formal karena skala penggunaan kecil dan dikelola langsung oleh Admin.

### C. Modul Kegiatan Harian / Logbook (Monitoring Only)
- **Ketua Kelompok** mengisi:
  - Tanggal, waktu mulai–selesai, deskripsi pekerjaan.
  - **Upload 1 foto dokumentasi**, maks **1MB/file** (kompresi otomatis tetap dijalankan agar file makin ringan, lihat §5B).
  - Tidak ada presensi individu di sistem — kehadiran mahasiswa sudah dipantau langsung oleh PIC Mitra di lokasi magang.
- **Konfirmasi "Sudah Dilihat" (bukan Approve/Reject)**:
  - PIC Mitra dan DPL masing-masing punya tombol **"Tandai Sudah Dilihat"** yang independen (1 klik dari HP) — fungsinya hanya memastikan mereka telah membaca deskripsi & foto kegiatan hari itu.
  - Tidak ada status `rejected` atau alur revisi. Sistem ini murni untuk pemantauan, bukan validasi/persetujuan formal.
  - Status yang ditampilkan cukup 2 indikator independen: **Dilihat PIC Mitra** (✓/belum) dan **Dilihat DPL** (✓/belum), masing-masing dengan timestamp otomatis saat ditandai.
  - Jika ketua salah input, ia tetap bisa **edit langsung** kapan saja (tanpa perlu proses reject formal) selama masih dalam masa PPL berjalan.

### D. Modul Luaran Akhir (Submission)
- Upload Laporan Akhir PDF (maks 10MB), validasi MIME type strict (`application/pdf` saja, cek magic bytes bukan hanya ekstensi).
- Input URL Video (YouTube/Drive) dengan validasi format URL.

### E. Modul Penilaian & Rekapitulasi
- Form PIC Mitra (60%): Disiplin & Presensi, Etika/Sikap, Kerja Sama, Kualitas Capaian Tugas.
- Form DPL (40%): Sistematika & Analisis Laporan, Kualitas & Kreativitas Video.
- Kalkulasi otomatis: `Nilai Akhir = (Skor Mitra × 0.60) + (Skor DPL × 0.40)`.
- **Tabel konversi nilai huruf (baru, wajib didefinisikan di awal, contoh)**:

| Rentang Angka | Huruf |
|---|---|
| 85–100 | A |
| 80–84.99 | B+ |
| 75–79.99 | B |
| 70–74.99 | C+ |
| 65–69.99 | C |
| < 65 | D |

  *(Rentang final harus dikonfirmasi ke pihak akademik fakultas — jangan hardcode di kode, simpan sebagai config/table agar bisa diubah tanpa deploy ulang.)*
- Ekspor rekap ke Excel/PDF.

### F. Modul Notifikasi
- **Reminder otomatis** (via cron harian) ke ketua kelompok jika logbook hari itu belum diisi hingga jam 20:00.
- **Reminder ke Admin** untuk kelompok yang logbook-nya **belum dilihat** PIC Mitra atau DPL selama > 3 hari berturut-turut — sekadar pengingat pemantauan, bukan eskalasi approval.
- Channel: in-app notification (bell icon) cukup untuk MVP; WhatsApp/email gateway bisa jadi fase 2 (opsional, tergantung budget API pihak ketiga).

---

## 5. Non-Functional, PWA & Mobile UI Guidelines

### A. Responsive Layout Rules (Bootstrap 5)
- **Desktop (`d-none d-md-table`)**: tabel lengkap.
- **Mobile (`d-block d-md-none`)**: card/timeline vertikal, *no horizontal scroll*.
- Bottom Navigation Bar fixed (Beranda, Logbook, Luaran, Profil).
- Tombol aksi minimal `padding: 10px 16px`, target sentuh ≥ 48×48px (WCAG).

### B. PWA Requirements
- `public/manifest.json`: `name`, `short_name`, `start_url: /dashboard`, `display: standalone`, `theme_color: #0d6efd`, ikon 192px & 512px.
- `public/sw.js`: cache aset statis, fallback `offline.html` saat koneksi terputus.
- **Catatan penting yang hilang di v1**: karena auth berbasis session (cookie), pastikan `sw.js` **tidak** meng-cache response API yang mengandung CSRF token/session-sensitive data — cukup cache aset statis (CSS/JS/logo), bukan halaman dashboard dinamis. Cache-first untuk aset statis, network-first untuk data.

---

## 6. Database Schema & Relations

Perubahan dari v2.0: dikembalikan ke **1 kolom foto** (bukan tabel terpisah), **tanpa tabel presensi**, dan status kegiatan disederhanakan jadi 2 flag independen "sudah dilihat" (tanpa `rejected`/log revisi). `soft delete`, `updated_at`, dan index tetap dipertahankan karena tidak menambah kompleksitas berarti namun tetap berguna untuk kerapian data jangka panjang.

Perubahan v2.2: ENUM `tipe` pada tabel `notifikasi` disederhanakan (hapus `approval`/`rejection`, sisa `reminder`/`sistem`) agar konsisten dengan penghapusan alur approve/reject.

```sql
-- 1. Master Pengguna
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'dpl', 'pic_mitra', 'ketua_kelompok') NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20),
    nip_nidn VARCHAR(30) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_role (role)
);

-- 2. Master Mitra
CREATE TABLE mitra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_mitra VARCHAR(150) NOT NULL,
    kategori ENUM('SKPD', 'Swasta', 'UMKM') NOT NULL,
    alamat TEXT,
    pic_user_id INT,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (pic_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_kategori (kategori)
);

-- 3. Kelompok PPL
CREATE TABLE kelompok_ppl (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelompok VARCHAR(100) NOT NULL,
    mitra_id INT NOT NULL,
    dpl_id INT NOT NULL,
    ketua_user_id INT NOT NULL,
    tahun_akademik VARCHAR(10) NOT NULL,
    status ENUM('aktif', 'selesai', 'dibatalkan') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mitra_id) REFERENCES mitra(id),
    FOREIGN KEY (dpl_id) REFERENCES users(id),
    FOREIGN KEY (ketua_user_id) REFERENCES users(id),
    INDEX idx_dpl (dpl_id),
    INDEX idx_tahun (tahun_akademik)
);

-- 4. Anggota Kelompok
CREATE TABLE anggota_kelompok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kelompok_id INT NOT NULL,
    nim VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    prodi ENUM('Manajemen', 'Akuntansi', 'Bisnis Digital') NOT NULL,
    FOREIGN KEY (kelompok_id) REFERENCES kelompok_ppl(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_nim_kelompok (kelompok_id, nim),
    INDEX idx_kelompok (kelompok_id)
);

-- 5. Kegiatan Harian (Logbook) — monitoring only, 1 foto, tanpa reject/revisi
CREATE TABLE kegiatan_harian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kelompok_id INT NOT NULL,
    tanggal DATE NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    deskripsi_kegiatan TEXT NOT NULL,
    foto_dokumentasi VARCHAR(255) NOT NULL, -- 1 file, maks 1MB (divalidasi di aplikasi)
    dilihat_mitra BOOLEAN DEFAULT FALSE,
    dilihat_mitra_at TIMESTAMP NULL,
    dilihat_dpl BOOLEAN DEFAULT FALSE,
    dilihat_dpl_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kelompok_id) REFERENCES kelompok_ppl(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_kelompok_tanggal (kelompok_id, tanggal),
    INDEX idx_dilihat (dilihat_mitra, dilihat_dpl)
);

-- 6. Luaran Akhir
CREATE TABLE luaran_kelompok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kelompok_id INT UNIQUE NOT NULL,
    file_laporan_pdf VARCHAR(255) NOT NULL,
    url_video VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kelompok_id) REFERENCES kelompok_ppl(id) ON DELETE CASCADE
);

-- 7. Penilaian Akhir
CREATE TABLE penilaian_ppl (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kelompok_id INT UNIQUE NOT NULL,
    skor_kedisiplinan DECIMAL(5,2),
    skor_etika DECIMAL(5,2),
    skor_kerjasama DECIMAL(5,2),
    skor_hasil_kerja DECIMAL(5,2),
    total_nilai_mitra DECIMAL(5,2),
    skor_laporan_pdf DECIMAL(5,2),
    skor_video DECIMAL(5,2),
    total_nilai_dpl DECIMAL(5,2),
    nilai_akhir_angka DECIMAL(5,2) GENERATED ALWAYS AS ((total_nilai_mitra * 0.60) + (total_nilai_dpl * 0.40)) STORED,
    nilai_huruf VARCHAR(2),
    catatan_evaluasi TEXT,
    dinilai_at TIMESTAMP NULL,
    FOREIGN KEY (kelompok_id) REFERENCES kelompok_ppl(id) ON DELETE CASCADE
);

-- 8. Notifikasi
CREATE TABLE notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    pesan TEXT NOT NULL,
    tipe ENUM('reminder', 'sistem') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_unread (user_id, is_read)
);
```

---

## 7. Security Requirements

| Area | Ketentuan |
|---|---|
| Password | Hash `bcrypt`/`argon2id` (default Laravel), minimal 8 karakter |
| Login | Rate limiting 5x/menit per IP + akun, lockout 15 menit |
| CSRF | Wajib token di semua form (default Laravel, jangan disable) |
| File Upload | Validasi MIME by magic bytes (bukan ekstensi), whitelist `.jpg/.jpeg/.png/.webp` untuk foto (**maks 1MB, 1 file per kegiatan**), `.pdf` untuk laporan (maks 10MB) — batas ukuran ditegakkan di server, bukan hanya client |
| R2 Storage | **Bucket private**, akses file via **signed URL** (expiring, misal 15 menit) dari backend — jangan expose kredensial R2 atau URL publik permanen |
| Data Pribadi | NIM, NIP/NIDN, no. HP adalah data pribadi (UU PDP No. 27/2022) — akses log/rekap dibatasi role, tidak diekspos di endpoint publik |
| Session | Regenerate session ID setelah login, `secure` + `httponly` cookie flag (HTTPS via Cloudflare wajib) |
| Backup | Backup DB harian otomatis (cron `mysqldump` + upload ke R2 terpisah dari bucket foto), retensi 30 hari |

---

## 8. Non-Functional Requirements (Terukur)

| Metrik | Target |
|---|---|
| Response time halaman dashboard | < 2 detik pada koneksi 4G |
| Concurrent user (estimasi) | 200–500 user aktif (skala fakultas), shared hosting perlu di-monitor CPU/RAM plan |
| Uptime | 99% (di luar maintenance window), realistis untuk shared hosting — bukan 99.9% |
| Ukuran foto | Maks **1MB/file** saat upload (divalidasi server); kompresi otomatis tetap dijalankan (resize maks 800px, WebP) untuk efisiensi storage & kuota mahasiswa |
| Kompatibilitas browser | Chrome/Safari mobile 2 versi terakhir |
| Skalabilitas | Jika user > 1000, evaluasi migrasi ke VPS (shared hosting punya limit proses PHP concurrent) |

---

## 9. Edge Cases & Operasional

| Skenario | Penanganan |
|---|---|
| Ketua kelompok mengundurkan diri/tidak aktif | Admin bisa reassign role ketua ke anggota lain via fitur reassignment (§4B), riwayat logbook tetap melekat ke kelompok bukan ke user |
| DPL cuti/sakit di tengah masa PPL | Admin reassign DPL via fitur pindah DPL (§4B); Admin memastikan manual total bimbingan DPL baru tetap ≤10 menggunakan info total read-only yang tersedia di halaman plotting |
| Logbook terlambat diisi (>1 hari) | Sistem tetap izinkan input mundur (backdate) tapi diberi flag "terlambat" untuk transparansi pemantauan Admin |
| PIC Mitra/DPL belum sempat "menandai dilihat" | Reminder otomatis ke Admin jika > 3 hari belum dilihat — sekadar pengingat, tidak memblokir proses apa pun |
| Mitra membatalkan kerja sama di tengah jalan | Admin update `mitra_id` di `kelompok_ppl` langsung; tidak perlu audit log formal mengingat skala penggunaan kecil |

---