@echo off
title Badminton Shop Laravel Server
cd /d %~dp0
echo ===================================================
echo     DANG KHOI CHAY SHOP CAU LONG (LARAVEL 11)
echo ===================================================
echo URL: http://127.0.0.1:8000
echo Tai khoan Admin: admin / admin123
echo Nhan Ctrl + C de dung server.
echo ---------------------------------------------------
if exist C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe (
    C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan serve --port=8000
) else (
    php artisan serve --port=8000
)
pause
