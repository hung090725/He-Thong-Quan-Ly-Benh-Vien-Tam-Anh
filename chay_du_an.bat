@echo off
cls
title HE THONG QUAN LY BENH VIEN DA KHOA TAM ANH

echo =======================================================
echo    HE THONG QUAN LY BENH VIEN DA KHOA TAM ANH
echo    Dang khoi dong may chu...
echo =======================================================
echo.

:: 1. Kiem tra va khoi dong MySQL
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe" >NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Co so du lieu MySQL da san sang.
) else (
    echo [..] Dang bat MySQL...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file=C:\xampp\mysql\bin\my.ini --console
        ping -n 3 127.0.0.1 >nul
        echo [OK] MySQL da bat thanh cong!
    )
)

echo.
echo [..] Dang mo trinh duyet web...
ping -n 2 127.0.0.1 >nul
start http://127.0.0.1:8000/login

echo.
echo =======================================================
echo   May chu dang chay tai: http://127.0.0.1:8000
echo   Tai khoan Le tan: receptionist@tamanh.vn ^| 123456
echo   (KHONG TAT cua so nay khi dang su dung web)
echo =======================================================
echo.

php artisan serve --host=0.0.0.0 --port=8000
