#!/bin/bash
# Script untuk memverifikasi dan memperbaiki image upload issue

echo "=== Verifikasi Image Upload Configuration ==="
echo ""

# 1. Check symbolic link
echo "1. Checking symbolic link..."
if [ -L "public/storage" ]; then
    echo "   ✓ Symbolic link EXISTS"
    echo "   Location: $(readlink -f public/storage)"
else
    echo "   ✗ Symbolic link NOT FOUND"
    echo "   Creating symbolic link..."
    php artisan storage:link
    echo "   ✓ Symbolic link created"
fi

echo ""

# 2. Check storage directories
echo "2. Checking storage directories..."
directories=(
    "storage/app/public/products"
    "storage/app/public/rental-items"
    "storage/app/public/payment-methods"
    "storage/app/public/logo"
)

for dir in "${directories[@]}"; do
    if [ -d "$dir" ]; then
        echo "   ✓ $dir exists"
    else
        echo "   ✗ $dir NOT FOUND - creating..."
        mkdir -p "$dir"
        echo "   ✓ Created: $dir"
    fi
done

echo ""

# 3. Check storage permissions
echo "3. Checking storage permissions..."
if [ -w "storage/app/public" ]; then
    echo "   ✓ storage/app/public is writable"
else
    echo "   ✗ storage/app/public is NOT writable"
    echo "   Setting permissions..."
    chmod -R 775 storage/app/public
    echo "   ✓ Permissions set to 775"
fi

echo ""

# 4. List current image files
echo "4. Current image files:"
echo "   Products:"
ls -la storage/app/public/products/ 2>/dev/null | tail -5 || echo "      No files"
echo ""
echo "   Rental Items:"
ls -la storage/app/public/rental-items/ 2>/dev/null | tail -5 || echo "      No files"
echo ""
echo "   Payment Methods:"
ls -la storage/app/public/payment-methods/ 2>/dev/null | tail -5 || echo "      No files"
echo ""
echo "   Logo:"
ls -la storage/app/public/logo/ 2>/dev/null | tail -5 || echo "      No files"

echo ""
echo "=== Verification Complete ==="
echo ""
echo "If you see any ✗ marks, run the following commands:"
echo "  1. php artisan storage:link"
echo "  2. chmod -R 775 storage/"
echo "  3. composer dump-autoload"
echo "  4. php artisan cache:clear"
