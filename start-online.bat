@echo off
title SI-IURAN RT - Cloudflare Tunnel Online Server
color 0A

echo ======================================================================
echo           MENYALAKAN SERVER IURAN RT + CLOUDFLARE TUNNEL
echo ======================================================================
echo.

:: 1. Tentukan binary PHP
set PHP_BIN=D:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe
if not exist "%PHP_BIN%" (
    set PHP_BIN=php
)

:: 2. Tentukan binary Cloudflared
set CF_BIN=D:\laragon\bin\cloudflared\cloudflared.exe
if not exist "%CF_BIN%" (
    echo [ERROR] cloudflared.exe tidak ditemukan di D:\laragon\bin\cloudflared\
    pause
    exit /b
)

echo [1/2] Menyalakan Laravel Server di port 8000...
start "Laravel Server (SI-IURAN RT)" /B "%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8000 > nul 2>&1

echo [2/2] Menghubungkan ke Cloudflare Network...
echo.
echo ======================================================================
echo   Perhatikan tulisan URL berakhiran .trycloudflare.com di bawah ini.
echo   URL HTTPS tersebut bisa dibuka dari HP, warga, maupun ditembak ESP32!
echo   (Biarkan jendela ini tetap terbuka agar web tetap online)
echo ======================================================================
echo.

"%CF_BIN%" tunnel --url http://127.0.0.1:8000
pause
