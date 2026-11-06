@echo off
title HUY TU DONG CHAY BENH VIEN TAM ANH KHI BAT MAY
color 0E
cls
echo ======================================================================
echo    HUY TU DONG KHOI DONG CUNG WINDOWS
echo ======================================================================
echo.
powershell -NoProfile -Command "Remove-Item ([System.IO.Path]::Combine([Environment]::GetFolderPath('Startup'), 'BenhVienTamAnh_AutoStart.lnk')) -ErrorAction SilentlyContinue"
echo.
echo [OK] DA HUY TU DONG KHOI DONG THANH CONG!
echo He thong se khong tu dong chay khi mo may nua.
echo Khi nao can dung, ban chi can nhap dup file KHOI_DONG_HE_THONG.bat.
echo.
echo ======================================================================
echo.
pause
