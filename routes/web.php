<?php

use App\Http\Controllers\Admin\BackupController as AdminBackupController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DplController as AdminDplController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\KelompokController as AdminKelompokController;
use App\Http\Controllers\Admin\LuaranController as AdminLuaranController;
use App\Http\Controllers\Admin\MahasiswaController as AdminMahasiswaController;
use App\Http\Controllers\Admin\MitraController as AdminMitraController;
use App\Http\Controllers\Admin\PenilaianController as AdminPenilaianController;
use App\Http\Controllers\Admin\PlottingController as AdminPlottingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

use App\Http\Controllers\Auth\AuthController as AuthenticatedSessionController;

use App\Http\Controllers\Dpl\DashboardController as DplDashboardController;
use App\Http\Controllers\Dpl\LogbookController as DplLogbookController;
use App\Http\Controllers\Dpl\LuaranController as DplLuaranController;
use App\Http\Controllers\Dpl\PenilaianController as DplPenilaianController;

use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Dpl\MonitoringController as DplMonitoringController;
use App\Http\Controllers\KetuaKelompok\MonitoringController as StudentMonitoringController;
use App\Http\Controllers\KetuaKelompok\DashboardController as StudentDashboardController;
use App\Http\Controllers\KetuaKelompok\LogbookController as StudentLogbookController;
use App\Http\Controllers\KetuaKelompok\LuaranController as StudentLuaranController;

use App\Http\Controllers\LogbookCetakPdfController;
use App\Http\Controllers\LogbookFotoController;
use App\Http\Controllers\LuaranFileController as LuaranDownloadController;
use App\Http\Controllers\NotifikasiController as NotificationController;
use App\Http\Controllers\PedomanController;

use App\Http\Controllers\PicMitra\DashboardController as PicDashboardController;
use App\Http\Controllers\PicMitra\LogbookController as PicLogbookController;
use App\Http\Controllers\PicMitra\PenilaianController as PicPenilaianController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
    Route::get('/forgot-password', [AuthenticatedSessionController::class, 'showForgotPasswordForm'])->name('password.request');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth'])->group(function () {

    // Must Change Password Enforcer & Manual Password Update Routes
    Route::get('/change-password', [AuthenticatedSessionController::class, 'showChangePasswordForm'])->name('password.change');
    Route::get('/change-password-form', [AuthenticatedSessionController::class, 'showChangePasswordForm'])->name('password.change.form');
    Route::post('/change-password', [AuthenticatedSessionController::class, 'updatePassword'])->name('password.change.update');
    Route::post('/update-password', [AuthenticatedSessionController::class, 'updatePassword'])->name('password.update');

    Route::middleware(['must.change.password'])->group(function () {

        // Global Pedoman / Buku Panduan PPL Route (Accessible across roles)
        Route::get('/pedoman', [PedomanController::class, 'index'])->name('pedoman.index');

        // Admin Role Routes
        Route::middleware(['role:admin'])->prefix('admin')->as('admin.')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('/backup/download', [AdminBackupController::class, 'downloadBackup'])->name('backup.download');
            
            // Master Data DPL
            Route::get('/dpl/export', [AdminDplController::class, 'exportExcel'])->name('dpl.export');
            Route::get('/dpl/template', [AdminDplController::class, 'downloadTemplate'])->name('dpl.template');
            Route::post('/dpl/import', [AdminDplController::class, 'importExcel'])->name('dpl.import');
            Route::resource('dpl', AdminDplController::class);
            
            // Master Data Mitra & Kelompok
            Route::get('/mitra/export', [AdminMitraController::class, 'exportExcel'])->name('mitra.export');
            Route::get('/mitra/template', [AdminMitraController::class, 'downloadTemplate'])->name('mitra.template');
            Route::post('/mitra/import', [AdminMitraController::class, 'importExcel'])->name('mitra.import');
            Route::resource('mitra', AdminMitraController::class);
            Route::get('/kelompok/export', [AdminKelompokController::class, 'exportExcel'])->name('kelompok.export');
            Route::get('/kelompok/template', [AdminKelompokController::class, 'downloadTemplate'])->name('kelompok.template');
            Route::post('/kelompok/import', [AdminKelompokController::class, 'importExcel'])->name('kelompok.import');
            Route::get('/kelompok/{kelompok}/logbook-pdf', [LogbookCetakPdfController::class, 'downloadPdf'])->name('kelompok.logbook.pdf');
            Route::resource('kelompok', AdminKelompokController::class);
            
            // Plotting Kelompok & Export Reports
            Route::get('/plotting/pdf', [AdminPlottingController::class, 'exportPdf'])->name('plotting.pdf');
            Route::get('/plotting/export-excel', [AdminPlottingController::class, 'exportExcel'])->name('plotting.export-excel');
            Route::resource('plotting', AdminPlottingController::class)->parameters(['plotting' => 'kelompok']);
            
            // Kelola User System & Sub-menu Kategori Akun
            Route::get('/users/dpl', [AdminUserController::class, 'categoryDpl'])->name('users.dpl');
            Route::get('/users/pic', [AdminUserController::class, 'categoryPic'])->name('users.pic');
            Route::get('/users/kelompok', [AdminUserController::class, 'categoryKelompok'])->name('users.kelompok');
            Route::resource('users', AdminUserController::class);

            // Master Data Mahasiswa Routes
            Route::get('/mahasiswa/pdf', [AdminMahasiswaController::class, 'exportPdf'])->name('mahasiswa.pdf');
            Route::get('/mahasiswa/export', [AdminMahasiswaController::class, 'exportExcel'])->name('mahasiswa.export');
            Route::get('/mahasiswa/template', [AdminMahasiswaController::class, 'downloadTemplate'])->name('mahasiswa.template');
            Route::post('/mahasiswa/import', [AdminMahasiswaController::class, 'importExcel'])->name('mahasiswa.import');
            Route::resource('mahasiswa', AdminMahasiswaController::class);

            Route::get('/luaran', [AdminLuaranController::class, 'index'])->name('luaran.index');

            // Input & Rekap Nilai PPL (Draft / Kunci) & Config Scale
            Route::get('/penilaian', [AdminPenilaianController::class, 'index'])->name('penilaian.index');
            Route::get('/penilaian/export', [AdminPenilaianController::class, 'export'])->name('penilaian.export');
            Route::post('/penilaian/scale', [AdminPenilaianController::class, 'updateGradeScale'])->name('penilaian.scale.update');
            Route::post('/penilaian/bulk', [AdminPenilaianController::class, 'bulk'])->name('penilaian.bulk');
            Route::put('/penilaian/{mahasiswa}', [AdminPenilaianController::class, 'update'])->name('penilaian.update');
            Route::post('/penilaian/{mahasiswa}/lock', [AdminPenilaianController::class, 'lock'])->name('penilaian.lock');
            Route::post('/penilaian/{mahasiswa}/unlock', [AdminPenilaianController::class, 'unlock'])->name('penilaian.unlock');

            // Export & Print PDF / Excel Routes
            Route::get('/export/lembar-nilai-pdf/{kelompok}', [AdminExportController::class, 'downloadLembarNilaiPdf'])->name('export.lembar-nilai.pdf');
            Route::get('/export/rekap-nilai-pdf', [AdminExportController::class, 'downloadRekapNilaiPdf'])->name('export.nilai.pdf');
            Route::get('/export/nilai-excel', [AdminPenilaianController::class, 'export'])->name('export.nilai.excel');
            Route::get('/export/kelompok-excel', [AdminExportController::class, 'exportKelompokExcel'])->name('export.kelompok.excel');
            Route::get('/export/mitra-excel', [AdminExportController::class, 'exportMitraExcel'])->name('export.mitra.excel');

            // Monitoring DPL Admin Summary
            Route::get('/monitoring', [AdminMonitoringController::class, 'index'])->name('monitoring.index');
        });

        // DPL Role Routes
        Route::middleware(['role:dpl'])->prefix('dpl')->as('dpl.')->group(function () {
            Route::get('/dashboard', [DplDashboardController::class, 'index'])->name('dashboard');
            Route::get('/logbook', [DplLogbookController::class, 'index'])->name('logbook.index');
            Route::get('/logbook/{kelompok}/pdf', [LogbookCetakPdfController::class, 'downloadPdf'])->name('logbook.pdf');
            Route::get('/logbook/{logbook}', [DplLogbookController::class, 'show'])->name('logbook.show');
            Route::post('/logbook/{logbook}/viewed', [DplLogbookController::class, 'markAsViewed'])->name('logbook.viewed');
            Route::get('/luaran', [DplLuaranController::class, 'index'])->name('luaran.index');

            // Input & Rekap Nilai Laporan DPL (40%)
            Route::get('/penilaian', [DplPenilaianController::class, 'index'])->name('penilaian.index');
            Route::get('/penilaian/export', [DplPenilaianController::class, 'export'])->name('penilaian.export');
            Route::post('/penilaian/bulk-lock', [DplPenilaianController::class, 'bulkLock'])->name('penilaian.bulk-lock');
            Route::get('/penilaian/{kelompok}/edit', [DplPenilaianController::class, 'edit'])->name('penilaian.edit');
            Route::put('/penilaian/{mahasiswa}', [DplPenilaianController::class, 'update'])->name('penilaian.update');
            Route::post('/penilaian/{mahasiswa}/lock', [DplPenilaianController::class, 'lock'])->name('penilaian.lock');

            // Monitoring DPL Kunjungan Lapangan
            Route::resource('monitoring', DplMonitoringController::class);
        });

        // PIC Mitra Role Routes
        Route::middleware(['role:pic_mitra'])->prefix('pic')->as('pic.')->group(function () {
            Route::get('/dashboard', [PicDashboardController::class, 'index'])->name('dashboard');
            Route::get('/logbook', [PicLogbookController::class, 'index'])->name('logbook.index');
            Route::get('/logbook/{logbook}', [PicLogbookController::class, 'show'])->name('logbook.show');
            Route::post('/logbook/{logbook}/viewed', [PicLogbookController::class, 'markAsViewed'])->name('logbook.viewed');
            Route::post('/logbook/{logbook}/paraf', [PicLogbookController::class, 'markAsViewed'])->name('logbook.paraf');

            // Penilaian PIC Mitra (60%)
            Route::get('/penilaian', [PicPenilaianController::class, 'index'])->name('penilaian.index');
            Route::post('/penilaian/{kelompok}', [PicPenilaianController::class, 'storeOrUpdate'])->name('penilaian.store');
        });

        // Student / Kelompok Role Routes
        Route::middleware(['role:ketua_kelompok'])->prefix('student')->as('student.')->group(function () {
            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
            Route::resource('logbook', StudentLogbookController::class);
            Route::get('/luaran', [StudentLuaranController::class, 'index'])->name('luaran.index');
            Route::post('/luaran', [StudentLuaranController::class, 'storeOrUpdate'])->name('luaran.store');

            // Monitoring DPL Approval by Student
            Route::get('/monitoring', [StudentMonitoringController::class, 'index'])->name('monitoring.index');
            Route::post('/monitoring/{monitoring}/approve', [StudentMonitoringController::class, 'approve'])->name('monitoring.approve');
        });

        // Alias for ketua route names
        Route::middleware(['role:ketua_kelompok'])->prefix('ketua')->as('ketua.')->group(function () {
            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
            Route::get('/logbook-pdf', [LogbookCetakPdfController::class, 'downloadPdf'])->name('logbook.pdf');
            Route::resource('logbook', StudentLogbookController::class);
            Route::get('/luaran', [StudentLuaranController::class, 'index'])->name('luaran.index');
            Route::post('/luaran', [StudentLuaranController::class, 'storeOrUpdate'])->name('luaran.store');

            // Monitoring DPL Approval by Student
            Route::get('/monitoring', [StudentMonitoringController::class, 'index'])->name('monitoring.index');
            Route::post('/monitoring/{monitoring}/approve', [StudentMonitoringController::class, 'approve'])->name('monitoring.approve');
        });

        // Prefix Root Direct Redirects (e.g. /pic -> /pic/dashboard, /admin -> /admin/dashboard)
        Route::get('/admin', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dpl', fn () => redirect()->route('dpl.dashboard'));
        Route::get('/pic', fn () => redirect()->route('pic.dashboard'));
        Route::get('/ketua', fn () => redirect()->route('ketua.dashboard'));
        Route::get('/student', fn () => redirect()->route('student.dashboard'));

        // Shared Foto & PDF Download Routes
        Route::get('/foto/{logbook}', [LogbookFotoController::class, 'show'])->name('logbook.foto');
        Route::get('/foto-show/{logbook}', [LogbookFotoController::class, 'show'])->name('foto.show');
        Route::get('/luaran/{luaran}/pdf-download', [LuaranDownloadController::class, 'download'])->name('luaran.pdf.download');
        Route::get('/luaran/pdf/{luaran}', [LuaranDownloadController::class, 'download'])->name('luaran.pdf.show');

        // Real-Time In-App Notifications API
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notifikasi}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

        Route::post('/notifikasi/{notifikasi}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllAsRead']);

        // Fallback Route for Authenticated Users (Handling Non-Existent or Invalid URLs gracefully)
        Route::fallback(function () {
            $user = auth()->user();
            if ($user) {
                $dashboardRoute = match ($user->role) {
                    'admin' => 'admin.dashboard',
                    'dpl' => 'dpl.dashboard',
                    'pic_mitra' => 'pic.dashboard',
                    'ketua_kelompok' => 'ketua.dashboard',
                    default => 'login',
                };
                return redirect()->route($dashboardRoute)->with('error', 'Halaman tidak ditemukan atau Anda tidak memiliki akses ke halaman tersebut.');
            }
            return redirect()->route('login');
        });
    });
});
