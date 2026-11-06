@echo off
chcp 65001 >nul
title HỆ THỐNG BỆNH VIỆN ĐA KHOA TÂM ANH - KHỞI ĐỘNG
color 0B
echo ======================================================================
echo    HỆ THỐNG QUẢN LÝ BỆNH VIỆN ĐA KHOA TÂM ANH
echo    Khởi động tự động: MySQL Database + Laravel Web Server
echo ======================================================================
echo.

:: 1. Kiểm tra MySQL
echo [1/3] Kiểm tra Cơ sở dữ liệu MySQL...
netstat -ano | findstr ":3306" | findstr "LISTENING" >nul
if %ERRORLEVEL% EQU 0 (
    echo    -> Cơ sở dữ liệu MySQL đã đang chạy sẵn sàng.
) else (
    echo    -> Đang khởi động MySQL Database...
    start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini"
    timeout /t 3 /nobreak >nul
    echo    -> Khởi động MySQL thành công!
)
echo.

:: 2. Kiểm tra Laravel Web Server
echo [2/3] Kiểm tra Web Server Bệnh Viện Tâm Anh...
netstat -ano | findstr ":8000" | findstr "LISTENING" >nul
if %ERRORLEVEL% EQU 0 (
    echo    -> Web Server (cổng 8000) đã đang chạy sẵn sàng.
) else (
    echo    -> Đang khởi động Web Server (hỗ trợ cả máy tính & điện thoại)...
    cd /d "C:\xampp\htdocs\benhvientamanh"
    start "He Thong Benh Vien Tam Anh (Web Server)" /min php artisan serve --host=0.0.0.0 --port=8000
    timeout /t 2 /nobreak >nul
    echo    -> Khởi động Web Server thành công!
)
echo.

:: 3. Mở Trình Duyệt
echo [3/3] Đang mở website Bệnh Viện Tâm Anh trên trình duyệt...
start http://127.0.0.1:8000
echo.
echo ======================================================================
echo    HỆ THỐNG ĐÃ SẴN SÀNG SỬ DỤNG!
echo.
echo    [1] Truy cập trên máy tính này:
echo        http://127.0.0.1:8000  hoặc  http://localhost:8000
echo.
echo    [2] Truy cập từ Điện thoại / Máy tính khác (cùng mạng Wifi):
echo        http://10.3.1.119:8000
echo.
echo    Tài khoản mẫu:
echo    - Quản trị viên:  admin@tamanh.vn        / Mật khẩu: 123456
echo    - Lễ tân:         receptionist@tamanh.vn / Mật khẩu: 123456
echo    - Bác sĩ:         doctor@tamanh.vn       / Mật khẩu: 123456
echo    - Bệnh nhân:      patient@gmail.com      / Mật khẩu: 123456
echo ======================================================================
echo.
echo (Bạn có thể thu nhỏ cửa sổ này để sử dụng web bình thường)
pause
