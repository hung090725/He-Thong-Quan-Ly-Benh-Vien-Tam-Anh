@echo off
chcp 65001 >nul
title CÀI ĐẶT TỰ ĐỘNG CHẠY BỆNH VIỆN TÂM ANH KHI BẬT MÁY
color 0A
echo ======================================================================
echo    CÀI ĐẶT TỰ ĐỘNG KHỞI ĐỘNG CÙNG WINDOWS
echo ======================================================================
echo.
powershell -Command "$ws = New-Object -ComObject WScript.Shell; $s = $ws.CreateShortcut([System.IO.Path]::Combine([Environment]::GetFolderPath('Startup'), 'BenhVienTamAnh_AutoStart.lnk')); $s.TargetPath = 'C:\xampp\htdocs\benhvientamanh\KHOI_DONG_HE_THONG.bat'; $s.WindowStyle = 7; $s.Save()"
echo.
echo [V] ĐÃ CÀI ĐẶT THÀNH CÔNG!
echo Từ bây giờ, mỗi khi bạn mở máy tính lên, Hệ thống Bệnh viện Tâm Anh
echo sẽ tự động chạy ngầm sẵn sàng. Bạn chỉ cần mở trình duyệt và sử dụng!
echo.
echo ======================================================================
pause
