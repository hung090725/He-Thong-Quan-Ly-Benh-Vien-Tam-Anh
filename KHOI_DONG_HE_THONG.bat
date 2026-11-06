@echo off
title HE THONG BENH VIEN DA KHOA TAM ANH - KHOI DONG
color 0B
cls
echo ======================================================================
echo    HE THONG QUAN LY BENH VIEN DA KHOA TAM ANH
echo    Khoi dong tu dong: MySQL Database + Laravel Web Server
echo ======================================================================
echo.

:: 1. Kiem tra MySQL
echo [1/3] Kiem tra Co so du lieu MySQL...
netstat -ano | findstr ":3306" | findstr "LISTENING" >nul
if errorlevel 1 (
    echo    -> Dang khoi dong MySQL Database...
    start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini"
    timeout /t 3 /nobreak >nul
    echo    -> Khoi dong MySQL thanh cong!
) else (
    echo    -> Co so du lieu MySQL da dang chay san sang.
)
echo.

:: 2. Kiem tra Laravel Web Server
echo [2/3] Kiem tra Web Server Benh Vien Tam Anh...
netstat -ano | findstr ":8000" | findstr "LISTENING" >nul
if errorlevel 1 (
    echo    -> Dang khoi dong Web Server...
    cd /d "C:\xampp\htdocs\benhvientamanh"
    start "He Thong Benh Vien Tam Anh" /min php artisan serve --host=0.0.0.0 --port=8000
    timeout /t 2 /nobreak >nul
    echo    -> Khoi dong Web Server thanh cong!
) else (
    echo    -> Web Server cong 8000 da dang chay san sang.
)
echo.

:: 3. Mo Trinh Duyet
echo [3/3] Dang mo website Benh Vien Tam Anh tren trinh duyet...
start http://127.0.0.1:8000
echo.
echo ======================================================================
echo    HE THONG DA SAN SANG SU DUNG!
echo.
echo    1. Truy cap tren may tinh nay:
echo       http://127.0.0.1:8000  hoac  http://localhost:8000
echo.
echo    2. Truy cap tu Dien thoai hoac May tinh khac qua Wifi:
echo       http://10.3.1.119:8000
echo.
echo    Tai khoan mau:
echo    - Quan tri:  admin@tamanh.vn        / Mat khau: 123456
echo    - Le tan:    receptionist@tamanh.vn / Mat khau: 123456
echo    - Bac si:    doctor@tamanh.vn       / Mat khau: 123456
echo    - Benh nhan: patient@gmail.com      / Mat khau: 123456
echo ======================================================================
echo.
echo Thu nho cua so nay xuong Taskbar va su dung web binh thuong.
echo Khong dong cua so neu muon web tiep tuc chay.
echo.
pause
