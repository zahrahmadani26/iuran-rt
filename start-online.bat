@echo off
title SI-IURAN RT - Permanent Ngrok Server
color 0A

:: Pastikan bekerja di folder script ini berada (D:\laragon\www\iuran-rt)
cd /d "%~dp0"

echo ======================================================================
echo           MENYALAKAN SERVER IURAN RT (PERMANENT DOMAIN NGROK)
echo ======================================================================
echo.

:: 1. Tentukan binary PHP
set PHP_BIN=D:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe
if not exist "%PHP_BIN%" (
    set PHP_BIN=php
)

:: 2. Tentukan binary Ngrok
set NGROK_BIN=D:\laragon\bin\ngrok\ngrok.exe
if not exist "%NGROK_BIN%" (
    set NGROK_BIN=ngrok
)

:: Hentikan instance php/ngrok lama jika ada
taskkill /F /IM php.exe > nul 2>&1
taskkill /F /IM ngrok.exe > nul 2>&1

echo [1/2] Menyalakan Laravel Server di port 8000...
start "Laravel Server (SI-IURAN RT)" "%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8000

:: Tunggu 2 detik agar server Laravel siap
timeout /t 2 /nobreak > nul

echo [2/2] Menghubungkan ke Domain Permanen Ngrok...
echo.
echo ======================================================================
echo   DOMAIN PERMANEN AKTIF (TIDAK AKAN BERUBAH MESKI LAPTOP MATI):
echo   👉 https://storeroom-exhale-pointless.ngrok-free.dev
echo.
echo   Login     : https://storeroom-exhale-pointless.ngrok-free.dev/login
echo   Simulator : https://storeroom-exhale-pointless.ngrok-free.dev/rfid
echo   API ESP32 : https://storeroom-exhale-pointless.ngrok-free.dev/api/rfid/tap
echo ======================================================================
echo.

"%NGROK_BIN%" http --url https://storeroom-exhale-pointless.ngrok-free.dev 8000
pause
