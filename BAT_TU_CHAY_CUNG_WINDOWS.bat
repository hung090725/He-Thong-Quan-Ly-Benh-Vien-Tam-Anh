@echo off
title CAI DAT TU DONG CHAY BENH VIEN TAM ANH KHI BAT MAY
color 0A
cls
echo ======================================================================
echo    CAI DAT TU DONG KHOI DONG CUNG WINDOWS
echo ======================================================================
echo.
powershell -NoProfile -Command "$ws = New-Object -ComObject WScript.Shell; $s = $ws.CreateShortcut([System.IO.Path]::Combine([Environment]::GetFolderPath('Startup'), 'BenhVienTamAnh_AutoStart.lnk')); $s.TargetPath = 'C:\xampp\htdocs\benhvientamanh\KHOI_DONG_HE_THONG.bat'; $s.WindowStyle = 7; $s.Save()"
echo.
echo [OK] DA CAI DAT THANH CONG!
echo Tu bay gio, moi khi ban mo may tinh len, He thong Benh vien Tam Anh
echo se tu dong chay san sang. Ban chi can mo trinh duyet va su dung!
echo.
echo ======================================================================
echo.
pause
