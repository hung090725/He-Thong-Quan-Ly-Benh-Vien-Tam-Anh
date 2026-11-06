@echo off
chcp 65001 >nul
title HỆ THỐNG QUẢN LÝ BỆNH VIỆN ĐA KHOA TÂM ANH
color 0B
cls

echo ===============================================================================
echo     🏥 HỆ THỐNG QUẢN LÝ BỆNH VIỆN ĐA KHOA TÂM ANH
echo     Khởi động máy chủ và mở ứng dụng trên trình duyệt
echo ===============================================================================
echo.

:: 1. Kiểm tra MySQL, nếu chưa chạy thì khởi động
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo  [✓] Cơ sở dữ liệu MySQL/MariaDB đang hoạt động.
) else (
    echo  [!] Đang khởi động MySQL/MariaDB...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file=C:\xampp\mysql\bin\my.ini --console
        timeout /t 2 /nobreak >nul
        echo  [✓] Khởi động MySQL thành công!
    ) else (
        echo  [!] Không tìm thấy MySQL tại C:\xampp\mysql. Vui lòng bật XAMPP Control Panel.
    )
)

echo.
echo  [+] Đang mở trang web Bệnh viện Tâm Anh trên trình duyệt...
start "" "http://127.0.0.1:8000/login"

echo.
echo ===============================================================================
echo   - Máy của bạn truy cập tại:  http://127.0.0.1:8000
echo   - Máy khác cùng mạng Wi-Fi:  http://10.3.1.119:8000
echo   - Tài khoản Lễ tân:          receptionist@tamanh.vn  ^|  Mật khẩu: 123456
echo   (Vui lòng GIỮ cửa sổ này trong suốt quá trình demo, KHÔNG tắt cửa sổ này)
echo ===============================================================================
echo.

php artisan serve --host=0.0.0.0 --port=8000
pause
