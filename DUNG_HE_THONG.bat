@echo off
chcp 65001 >nul
title DỪNG HỆ THỐNG BỆNH VIỆN ĐA KHOA TÂM ANH
color 0C
echo ======================================================================
echo    ĐANG DỪNG CÁC DỊCH VỤ BỆNH VIỆN TÂM ANH...
echo ======================================================================
echo.
echo Đang tắt Laravel Web Server...
taskkill /F /IM php.exe /FI "WINDOWTITLE eq He Thong Benh Vien Tam Anh*" >nul 2>&1
echo Đang tắt MySQL Database...
"C:\xampp\mysql\bin\mysqladmin.exe" -u root shutdown >nul 2>&1
echo.
echo Đã dừng toàn bộ dịch vụ an toàn.
echo.
timeout /t 3 >nul
