@echo off
chcp 65001 >nul
title HỦY TỰ ĐỘNG CHẠY BỆNH VIỆN TÂM ANH KHI BẬT MÁY
color 0E
echo ======================================================================
echo    HỦY TỰ ĐỘNG KHỞI ĐỘNG CÙNG WINDOWS
echo ======================================================================
echo.
powershell -Command "Remove-Item ([System.IO.Path]::Combine([Environment]::GetFolderPath('Startup'), 'BenhVienTamAnh_AutoStart.lnk')) -ErrorAction SilentlyContinue"
echo.
echo [V] ĐÃ HỦY TỰ ĐỘNG KHỞI ĐỘNG THÀNH CÔNG!
echo Hệ thống sẽ không tự chạy khi mở máy nữa.
echo Khi nào cần dùng, bạn chỉ cần nhấp đúp file KHOI_DONG_HE_THONG.bat trên Desktop.
echo.
echo ======================================================================
pause
