@echo off
title SI-IURAN RT - Laravel Server (127.0.0.1:8000)
cd /d "D:\laragon\www\iuran-rt"
echo ========================================================
echo   SI-IURAN RT - Menjalankan Website Laravel di Localhost
echo ========================================================
echo.
echo URL Website : http://127.0.0.1:8000
echo.
echo Akun Login Bawaan (Hasil Seeding):
echo 1. Admin RT     : admin@rt.com     / password
echo 2. Bendahara RT : bendahara@rt.com / password
echo 3. Warga        : warga@rt.com     / password
echo.
echo Membuka browser ke http://127.0.0.1:8000 ...
start http://127.0.0.1:8000
echo.
echo Tekan Ctrl+C untuk menghentikan server.
echo ========================================================
"D:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe" artisan serve --host=127.0.0.1 --port=8000
pause
