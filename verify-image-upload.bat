#!/bin/bash
# Windows batch file for verification

@echo off
echo === Verifikasi Image Upload Configuration ===
echo.

echo 1. Checking if storage directories exist...
if not exist "storage\app\public\products" (
    echo   Creating products directory...
    mkdir "storage\app\public\products"
)
echo   ✓ products directory ready

if not exist "storage\app\public\rental-items" (
    echo   Creating rental-items directory...
    mkdir "storage\app\public\rental-items"
)
echo   ✓ rental-items directory ready

if not exist "storage\app\public\payment-methods" (
    echo   Creating payment-methods directory...
    mkdir "storage\app\public\payment-methods"
)
echo   ✓ payment-methods directory ready

if not exist "storage\app\public\logo" (
    echo   Creating logo directory...
    mkdir "storage\app\public\logo"
)
echo   ✓ logo directory ready

echo.
echo 2. Checking symbolic link...
if exist "public\storage" (
    echo   ✓ Symbolic link already exists
) else (
    echo   Creating symbolic link...
    php artisan storage:link
)

echo.
echo 3. Clear cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo === Verification Complete ===
echo.
echo Next steps:
echo 1. Test upload gambar produk
echo 2. Pastikan gambar tersimpan di storage/app/public/products/
echo 3. Verifikasi gambar tampil dengan benar di dashboard
echo.
pause
