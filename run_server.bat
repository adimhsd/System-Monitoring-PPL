@echo off
title System Monitoring PPL FEB UNIKU - Server Launcher
echo ========================================================
echo   Sistem Monitoring PPL FEB Universitas Kuningan
echo   Memulai Peladen Aplikasi (Server & Queue Worker)...
echo ========================================================
echo.

start "Laravel Server (Port 8000)" cmd /k "cd /d C:\SystemMonitoringPPL && php artisan serve"
start "Laravel Queue Worker" cmd /k "cd /d C:\SystemMonitoringPPL && php artisan queue:work"

echo [OK] Peladen berhasil diaktifkan!
echo Silakan buka browser di: http://127.0.0.1:8000
echo.
pause
