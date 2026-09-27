# EXECUTION_PROMPT.md
# Sistem Monitoring PPL FEB — Implementation Execution Prompt
# Berdasarkan: PRD_Sistem_Monitoring_PPL_FEB_v2.2.md

---

## PERAN & KONTEKS UNTUK AI CODING AGENT

Kamu adalah AI coding agent yang bertugas mengimplementasikan **Sistem Monitoring PPL FEB** sesuai PRD v2.2 terlampir. Ini adalah aplikasi **monitoring**, BUKAN sistem approval/workflow formal. Prinsip yang WAJIB dipegang di semua fase:

- **Tidak ada presensi individu digital** — jangan buat tabel/fitur presensi anggota.
- **Tidak ada alur approve/reject/revisi** — hanya flag independen "sudah dilihat" (`dilihat_mitra`, `dilihat_dpl`).
- **Validasi kuota DPL (≤10) dilakukan manual oleh Admin** — sistem hanya menampilkan info read-only, jangan buat validasi blocking otomatis.
- **1 foto per kegiatan, maks 1MB** — jangan implementasi multi-foto/galeri.
- Target deployment: **cPanel shared hosting** — hindari dependency yang butuh `supervisor`, gunakan **database queue driver + cron**, hindari proses long-running.
- Ikuti tech stack di §2 PRD secara ketat: **Laravel 11.x, Blade + Bootstrap 5.3 + Alpine.js, MySQL 8.x, Cloudflare R2 (private bucket)**.

Kerjakan fase demi fase secara berurutan. Setiap fase harus **selesai dan bisa diuji (testable)** sebelum lanjut ke fase berikutnya. Di akhir tiap fase, laporkan ringkasan: file yang dibuat/diubah, migrasi yang dijalankan, dan langkah manual (jika ada) yang perlu dilakukan Adi (mis. isi `.env`, buat bucket R2).

---

## FASE 1 — Project Foundation & Environment Setup

**Tujuan**: Laravel 11 project siap jalan lokal, terkoneksi MySQL, siap untuk WSL2 Ubuntu (Docker Postgres tidak dipakai di sini — pakai MySQL native/Docker sesuai §2).

1. Inisialisasi project Laravel 11 (PHP 8.2+), buang starter kit yang tidak perlu (gunakan Blade default, bukan Breeze/Jetstream React/Vue).
2. Setup `.env.example` lengkap: DB (MySQL, `strict mode` aktif), `QUEUE_CONNECTION=database`, kredensial R2 (S3-compatible: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION=auto`, `AWS_BUCKET`, `AWS_ENDPOINT`, `AWS_USE_PATH_STYLE_ENDPOINT=true`), `SENTRY_LARAVEL_DSN`, `MAIL_*` untuk reset password.
3. Install & konfigurasi package: `league/flysystem-aws-s3-v3` (untuk R2), `maatwebsite/excel`, `barryvdh/laravel-dompdf`, `sentry/sentry-laravel`, `intervention/image` (untuk kompresi/resize foto).
4. Setup filesystem disk `r2` di `config/filesystems.php` dengan `visibility: private`.
5. Buat struktur folder dasar: `app/Models`, `app/Http/Controllers/{Admin,KetuaKelompok,PicMitra,Dpl}`, `app/Services`, `app/Notifications`.
6. Setup Bootstrap 5.3 + Alpine.js via Vite (bukan Bootstrap Studio/CDN, agar bisa di-bundle).
7. Konfigurasi `config/logging.php` — channel `daily`, retensi log wajar (14 hari).

**Deliverable**: `php artisan serve` jalan, koneksi DB sukses, `php artisan storage:link` (untuk asset lokal non-R2 seperti favicon/logo statis) berhasil.

---

## FASE 2 — Database Schema & Migrations

**Tujuan**: Implementasi skema DB sesuai §6 PRD v2.2 persis — 8 tabel, tanpa tabel presensi.

1. Buat migration untuk setiap tabel sesuai urutan dependency: `users` → `mitra` → `kelompok_ppl` → `anggota_kelompok` → `kegiatan_harian` → `luaran_kelompok` → `penilaian_ppl` → `notifikasi`.
2. Pastikan constraint persis sesuai PRD:
   - `kegiatan_harian`: kolom `foto_dokumentasi` **tunggal** (VARCHAR, bukan tabel/JSON terpisah), `dilihat_mitra` & `dilihat_dpl` sebagai BOOLEAN + timestamp masing-masing, `UNIQUE (kelompok_id, tanggal)`.
   - `notifikasi.tipe`: ENUM **hanya** `('reminder', 'sistem')` — jangan tambahkan `approval`/`rejection`.
   - `penilaian_ppl.nilai_akhir_angka`: **generated column** (STORED) dari formula `(total_nilai_mitra * 0.60) + (total_nilai_dpl * 0.40)`.
   - Soft delete (`deleted_at`) hanya di `users` dan `mitra` sesuai PRD.
3. Buat seluruh index sesuai PRD (`idx_role`, `idx_kategori`, `idx_dpl`, `idx_tahun`, `idx_kelompok`, `idx_dilihat`, `idx_user_unread`).
4. Buat model Eloquent untuk tiap tabel dengan relasi lengkap (`hasMany`/`belongsTo`), termasuk `$casts` untuk boolean & timestamp, dan `$fillable`.
5. Buat `DatabaseSeeder` dengan data dummy realistis: 1 admin, 3 DPL, 3 mitra (1 SKPD, 1 swasta, 1 UMKM), 5 kelompok, tiap kelompok 2-4 anggota, beberapa entri `kegiatan_harian` dengan variasi status dilihat.
6. Buat tabel `config` sederhana (key-value, bisa pakai package `spatie/laravel-settings` atau tabel custom) untuk menyimpan **tabel konversi nilai huruf** (§4E) — JANGAN hardcode di kode aplikasi.

**Deliverable**: `php artisan migrate:fresh --seed` sukses tanpa error, ERD konsisten dengan §6.

---

## FASE 3 — Autentikasi, RBAC & Keamanan Dasar

**Tujuan**: Login multi-role aman sesuai §4A dan §7.

1. Implementasi login berbasis session Laravel (Blade form, bukan API token).
2. Middleware RBAC custom (`role:admin`, `role:dpl`, dst.) yang membatasi akses route per role sesuai matrix §3.
3. Rate limiting login: 5 percobaan/menit per IP+akun, lockout 15 menit (gunakan `RateLimiter` facade Laravel, bukan package eksternal berat).
4. Reset password via token email (gunakan notifikasi Laravel bawaan `ResetPassword`), **bukan** self-reset tanpa verifikasi.
5. Wajib ganti password default saat login pertama — tambah kolom flag `must_change_password` di `users` (migration tambahan) dan middleware redirect paksa ke halaman ganti password.
6. Regenerate session ID setelah login (`$request->session()->regenerate()`), pastikan cookie `secure` + `httponly` aktif di `config/session.php`.
7. CSRF token aktif di semua form (default Laravel — jangan disable di middleware manapun).
8. Setup Sentry untuk error tracking production, pastikan Telescope **tidak** diaktifkan.

**Deliverable**: Login/logout per role berfungsi, rate limiting teruji (coba 6x salah password → lockout), redirect dashboard sesuai role.

---

## FASE 4 — Modul Master Data & Manajemen Kelompok/Plotting (Admin)

**Tujuan**: Implementasi §4B — Admin bisa kelola mitra, kelompok, plotting, dan reassignment.

1. CRUD Mitra (nama, kategori SKPD/Swasta/UMKM, alamat, akun PIC terkait).
2. CRUD Kelompok PPL: assign mahasiswa ke kelompok, assign mitra, assign DPL, assign ketua.
3. Halaman plotting: saat memilih DPL untuk kelompok, tampilkan **info total mahasiswa bimbingan DPL tersebut secara read-only** (hitung dari `kelompok_ppl` yang sudah ter-assign + anggota). **Jangan buat validasi blocking** jika total > 10 — cukup tampilkan sebagai info/warning visual (mis. badge merah), Admin tetap bisa submit.
4. Fitur "pindah DPL / pindah mitra / reassign ketua" — form update sederhana, tanpa tabel audit log terpisah (sesuai keputusan §9, skala kecil).
5. Registrasi akun PIC Mitra yang terhubung ke `mitra_id` terkait.
6. Dashboard Admin: ringkasan jumlah kelompok aktif, kelompok belum lengkap datanya, dan (siapkan slot untuk) widget keterlambatan logbook yang akan diisi di Fase 5.

**Deliverable**: Admin bisa buat mitra → buat kelompok → assign DPL (dengan info kuota visual) → assign anggota, end-to-end via UI.

---

## FASE 5 — Modul Logbook Harian (Kegiatan Harian) + Upload Foto

**Tujuan**: Implementasi §4C — logbook monitoring-only, 1 foto, kompresi async.

1. Form input logbook untuk Ketua Kelompok: tanggal (default hari ini, izinkan backdate), waktu mulai-selesai, deskripsi, upload 1 foto.
2. Validasi upload: MIME check via **magic bytes** (bukan cuma ekstensi), whitelist `.jpg/.jpeg/.png/.webp`, maks 1MB **di server** (bukan hanya client-side).
3. **Queue job** (database driver) untuk kompresi foto async: resize maks 800px, convert ke WebP, baru setelah itu upload ke R2 (private bucket) — pastikan proses upload/kompresi **tidak blocking request** (sesuai catatan shared hosting di §2).
4. Setup cron `queue:work --stop-when-empty` via scheduler (`* * * * * php artisan schedule:run`), daftarkan job compress-and-upload di `routes/console.php` atau scheduler.
5. Signed URL generation untuk menampilkan foto (expiring ±15 menit) — jangan expose URL R2 permanen.
6. Flag "terlambat" otomatis jika `tanggal` logbook di-backdate lebih dari 1 hari dari `created_at` (sesuai §9).
7. **Edit langsung** oleh ketua kapan saja selama masa PPL aktif — tanpa proses reject formal, cukup update record.
8. Constraint `UNIQUE(kelompok_id, tanggal)` — 1 kelompok hanya bisa 1 entri logbook per tanggal; tampilkan pesan jelas jika sudah ada (arahkan ke mode edit, bukan create baru).
9. Widget dashboard Admin (lanjutan Fase 4): daftar kelompok yang belum isi logbook >24 jam.

**Deliverable**: Ketua bisa submit logbook + foto, foto ter-kompresi async lewat queue, tampil dengan signed URL, flag terlambat berfungsi.

---

## FASE 6 — Modul Konfirmasi "Sudah Dilihat" (PIC Mitra & DPL)

**Tujuan**: Implementasi bagian kedua §4C — **bukan approve/reject**.

1. Halaman list logbook untuk PIC Mitra dan DPL (per kelompok bimbingan masing-masing), tampilkan deskripsi + foto (via signed URL).
2. Tombol **"Tandai Sudah Dilihat"** — satu tap, set `dilihat_mitra=true` + `dilihat_mitra_at=now()` (untuk PIC) atau `dilihat_dpl=true` + `dilihat_dpl_at=now()` (untuk DPL). **Independen satu sama lain**, tidak saling bergantung.
3. UI dioptimalkan untuk mobile (1 tap dari HP sesuai PRD) — tombol besar, minimal `padding: 10px 16px`, target sentuh ≥48×48px.
4. **Tidak ada** tombol reject/tolak/minta revisi di UI manapun.
5. Indikator status di list: 2 badge independen — "Dilihat PIC Mitra ✓/belum" dan "Dilihat DPL ✓/belum" dengan timestamp.
6. Widget dashboard Admin (lanjutan): daftar kelompok yang logbooknya belum ditandai dilihat >3 hari berturut-turut oleh PIC/DPL.

**Deliverable**: PIC Mitra dan DPL masing-masing bisa menandai logbook harian sebagai "sudah dilihat" secara independen dari HP.

---

## FASE 7 — Modul Luaran Akhir & Penilaian/Rekapitulasi

**Tujuan**: Implementasi §4D dan §4E.

1. Form upload Laporan Akhir PDF (maks 10MB, validasi `application/pdf` via magic bytes) + input URL video (validasi format URL YouTube/Drive) oleh Ketua Kelompok.
2. Form penilaian PIC Mitra (60%): 4 komponen skor (Disiplin & Presensi, Etika/Sikap, Kerja Sama, Kualitas Capaian Tugas).
3. Form penilaian DPL (40%): 2 komponen skor (Sistematika & Analisis Laporan, Kualitas & Kreativitas Video).
4. Kalkulasi otomatis `nilai_akhir_angka` (sudah generated column di DB — pastikan model/service membaca nilai ini, tidak menghitung ulang manual di PHP agar konsisten).
5. Konversi ke nilai huruf berdasarkan tabel konfigurasi dari Fase 2 (bukan hardcode) — buat `Service` class `NilaiHurufService` yang membaca dari tabel config.
6. Ekspor rekap nilai ke Excel (`maatwebsite/excel`) dan PDF (`barryvdh/laravel-dompdf`) untuk Admin, filter per angkatan/prodi/mitra.

**Deliverable**: End-to-end penilaian dari input skor mitra+DPL sampai rekap nilai huruf bisa diekspor.

---

## FASE 8 — Modul Notifikasi & Reminder Cron

**Tujuan**: Implementasi §4F.

1. Model & tabel `notifikasi` sudah ada dari Fase 2 (ENUM `tipe`: `reminder`, `sistem` saja).
2. Scheduled command harian (via `routes/console.php` + cron `schedule:run`):
   - Jam 20:00: cek kelompok yang belum isi logbook hari itu → kirim notifikasi in-app ke ketua kelompok.
   - Cek harian: kelompok dengan logbook belum ditandai dilihat PIC/DPL >3 hari berturut-turut → notifikasi in-app ke Admin.
3. UI bell icon in-app notification (Alpine.js dropdown, polling sederhana atau load saat page refresh — **jangan** implementasi WebSocket/Pusher, di luar scope MVP).
4. Mark as read per notifikasi.
5. Siapkan struktur agar channel WhatsApp/email mudah ditambahkan di Fase 2 pengembangan (interface `NotificationChannel`), tapi **jangan implementasi WhatsApp gateway sekarang** — itu eksplisit out of scope (§10).

**Deliverable**: Reminder otomatis jalan via cron, notifikasi muncul di bell icon in-app, ter-mark read.

---

## FASE 9 — PWA, Responsive UI & Compliance Data Pribadi

**Tujuan**: Implementasi §5 dan bagian data pribadi §7.

1. `public/manifest.json` lengkap sesuai spesifikasi PRD (`name`, `short_name`, `start_url: /dashboard`, `display: standalone`, `theme_color: #0d6efd`, ikon 192px & 512px).
2. `public/sw.js`: cache-first untuk aset statis (CSS/JS/logo), network-first untuk data. **Pastikan tidak meng-cache** response API yang mengandung CSRF token atau data session-sensitive — hanya cache aset statis, bukan halaman dashboard dinamis.
3. Fallback `offline.html` saat koneksi terputus.
4. Review seluruh halaman: layout desktop pakai `d-none d-md-table` untuk tabel, mobile pakai `d-block d-md-none` untuk card/timeline vertikal, no horizontal scroll.
5. Bottom navigation bar fixed (Beranda, Logbook, Luaran, Profil) — tampil di role Ketua Kelompok minimal; sesuaikan menu per role.
6. Audit akses data pribadi (NIM, NIP/NIDN, no. HP) — pastikan endpoint API/halaman rekap dibatasi role via middleware, tidak ada endpoint publik yang expose data ini (cross-check dengan §7).

**Deliverable**: App installable sebagai PWA, layout mobile-first konsisten di semua halaman, audit data pribadi lolos checklist manual.

---

## FASE 10 — Hardening, Backup, Testing & Deployment Prep (cPanel)

**Tujuan**: Siap deploy ke shared hosting sesuai §7 dan §8.

1. Setup backup DB harian otomatis: cron `mysqldump` → upload ke bucket R2 **terpisah** dari bucket foto, retensi 30 hari (buat script + jadwalkan via cron cPanel, dokumentasikan di README).
2. Review ulang seluruh file upload path — pastikan validasi ukuran & MIME ditegakkan di server untuk **semua** endpoint upload (foto logbook, PDF laporan), bukan cuma client-side.
3. Load testing dasar (bisa pakai `k6` atau sekadar simulasi paralel request) untuk target 200–500 concurrent user pada shared hosting — dokumentasikan hasil dan rekomendasi PHP-FPM/plan hosting jika ada bottleneck.
4. Pastikan Telescope non-aktif, Sentry aktif dan tertest (trigger error dummy, cek masuk ke Sentry dashboard).
5. Tulis `DEPLOYMENT.md`: langkah setup di cPanel (upload via Git/FTP, `.env` production, cron jobs yang perlu didaftarkan — `schedule:run` tiap menit, backup harian, reminder notifikasi), cara setup R2 bucket + CORS policy.
6. Tulis `README.md` ringkas: struktur role, cara jalankan lokal (WSL2 Ubuntu + Docker MySQL sesuai environment kerja Adi), cara migrate+seed.
7. Checklist akhir manual terhadap seluruh §7 (Security Requirements) dan §8 (NFR) — tandai item yang sudah terverifikasi.

**Deliverable**: Aplikasi siap deploy ke cPanel shared hosting, dokumentasi deployment & backup lengkap, checklist keamanan tercentang.

---

## CATATAN GAP ANALYSIS (perlu keputusan Adi sebelum/selama eksekusi)

Beberapa hal di PRD v2.2 belum sepenuhnya presisi secara teknis dan sebaiknya dikonfirmasi sebelum agent mulai coding, mengikuti pola gap analysis yang biasa dipakai:

1. **Migrasi data** — PRD tidak menyebut apakah ada data existing (mis. data mitra/DPL dari sistem manual sebelumnya) yang perlu di-import. Jika ada, perlu fase migrasi data tambahan sebelum go-live.
2. **Provisioning infrastruktur** — belum ada detail siapa yang setup akun Cloudflare R2, kredensial cPanel, dan domain — perlu dipastikan sebelum Fase 1 selesai agar `.env` production bisa diisi.
3. **Definisi "masa PPL berjalan"** — §4C menyebut ketua bisa edit logbook "selama masih dalam masa PPL berjalan", tapi tidak ada field eksplisit tanggal mulai/selesai PPL per kelompok di skema §6. Perlu ditambahkan kolom `tanggal_mulai`/`tanggal_selesai` di `kelompok_ppl` agar validasi ini bisa diimplementasi secara otomatis — disarankan ditambahkan sebagai migration kecil di Fase 2.
4. **Rentang nilai huruf final** — PRD sudah mencatat perlu konfirmasi ke pihak akademik; agent sebaiknya pakai nilai default di seed tapi jangan anggap final.

Silakan konfirmasi poin 1–3 sebelum eksekusi Fase 2 dan 5, karena keduanya menyentuh skema database yang lebih murah diperbaiki di awal daripada setelah data produksi masuk.