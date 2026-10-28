@echo off
setlocal
title Badminton Shop Laravel Server
cd /d %~dp0
echo ===================================================
echo     DANG KHOI CHAY SHOP CAU LONG (LARAVEL)
echo ===================================================
echo URL: http://127.0.0.1:8000
echo Trang quan tri: http://127.0.0.1:8000/admin  (admin / admin@123)
echo Nhan Ctrl + C de dung server.
echo ---------------------------------------------------

rem Tim php.exe: PATH -> C:\laragon -> thu muc laragon canh du an (..\..\laragon)
set "PHP="
where php >nul 2>nul && set "PHP=php"
if not defined PHP (
    for /d %%D in ("C:\laragon\bin\php\php-*" "%~dp0..\..\laragon\bin\php\php-*") do (
        if exist "%%~D\php.exe" set "PHP=%%~D\php.exe"
    )
)
if not defined PHP (
    echo Khong tim thay PHP. Hay cai Laragon hoac them php vao bien moi truong PATH.
    pause
    exit /b 1
)

if not exist vendor\autoload.php (
    echo Chua co thu muc vendor. Hay chay: composer install
    pause
    exit /b 1
)

"%PHP%" artisan serve --port=8000
pause
