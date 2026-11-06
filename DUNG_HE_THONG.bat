@echo off
title DUNG HE THONG BENH VIEN DA KHOA TAM ANH
color 0C
cls
echo ======================================================================
echo    DANG DUNG CAC DICH VU BENH VIEN TAM ANH...
echo ======================================================================
echo.
echo Dang tat Laravel Web Server...
taskkill /F /IM php.exe /FI "WINDOWTITLE eq He Thong Benh Vien Tam Anh*" >nul 2>&1
echo Dang tat MySQL Database...
"C:\xampp\mysql\bin\mysqladmin.exe" -u root shutdown >nul 2>&1
echo.
echo Da dung tat ca cac dich vu an toan.
echo.
timeout /t 3 >nul
