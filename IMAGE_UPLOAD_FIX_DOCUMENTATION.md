# Dokumentasi Perbaikan Image Upload Issue

## Masalah yang Ditemukan

Ketika user melakukan upload gambar di dashboard (Produk, Rental Items, Payment Methods, Logo Toko), gambar yang ditampilkan berbeda dengan yang tersimpan, sehingga gambar tidak terdeteksi dengan baik.

### Root Cause

Ada beberapa masalah konsistensi dalam penanganan image upload:

1. **Inkonsistensi FileUpload Configuration**
    - Tidak semua `FileUpload::make()` component meng-specify `disk('public')` dan `visibility('public')`
    - Beberapa tidak menggunakan `salvagePercentage(100)` yang bisa menyebabkan file loss saat error
    - Directory path tidak konsisten (ada yang menggunakan 'images', ada yang lain)

2. **Inkonsistensi Penyimpanan Logo**
    - SettingResource menggunakan `directory('images')` sementara yang lain lebih spesifik
    - Tidak ada disk specification yang jelas

3. **Masalah di PDF Views**
    - File `pengeluaran.blade.php` dan `pemasukan.blade.php` tidak melakukan cek `file_exists()` sebelum menampilkan gambar
    - Bisa menyebabkan error jika file tidak ditemukan

## Solusi yang Diterapkan

### 1. Perbaikan FileUpload Components

**File yang dimodifikasi:**

- `app/Filament/Resources/ProductResource.php`
- `app/Filament/Resources/RentalItemResource.php`
- `app/Filament/Resources/PaymentMethodResource.php`
- `app/Filament/Resources/SettingResource.php`

**Perubahan yang dilakukan:**

```php
// SEBELUM (SALAH)
FileUpload::make('image')
    ->label('Gambar Produk')
    ->disk('public')
    ->directory('products')
    ->visibility('public')
    ->image(),

// SESUDAH (BENAR)
FileUpload::make('image')
    ->label('Gambar Produk')
    ->disk('public')
    ->directory('products')
    ->visibility('public')
    ->image()
    ->salvagePercentage(100),  // Tambahan penting!
```

**Penjelasan:**

- `disk('public')` - Menyimpan file ke folder `storage/app/public/`
- `visibility('public')` - Membuat file dapat diakses public melalui symlink
- `salvagePercentage(100)` - Memastikan file upload tidak hilang (100% salvage rate)
- `directory('...')` - Direktori spesifik untuk setiap jenis file

**Directory Structure yang Digunakan:**

```
storage/app/public/
├── products/          # Gambar produk
├── rental-items/      # Gambar rental items
├── payment-methods/   # Icon metode pembayaran
└── logo/              # Logo toko
```

### 2. Perbaikan SettingResource

**Perubahan:**

```php
// SEBELUM
Forms\Components\FileUpload::make('logo')
    ->image()
    ->required()
    ->helperText('Pastikan format gambar adalah PNG')
    ->directory('images')
    ->label('Logo Toko'),

// SESUDAH
Forms\Components\FileUpload::make('logo')
    ->image()
    ->required()
    ->disk('public')
    ->directory('logo')
    ->visibility('public')
    ->salvagePercentage(100)
    ->helperText('Pastikan format gambar adalah PNG')
    ->label('Logo Toko'),

// Juga tambahan pada ImageColumn
Tables\Columns\ImageColumn::make('logo')
    ->circular()
    ->disk('public')  // Tambahan ini penting!
    ->label('Logo Toko'),
```

### 3. Perbaikan PDF Views

**File yang dimodifikasi:**

- `resources/views/pdf/reports/pengeluaran.blade.php`
- `resources/views/pdf/reports/pemasukan.blade.php`

**Perubahan:**

```blade
{{-- SEBELUM (SALAH) --}}
<div id="logo">
    <img src="{{ storage_path('app/public/' . $logo) }}" alt="{{ asset('storage/' . $logo) }}">
</div>

{{-- SESUDAH (BENAR) --}}
<div id="logo">
    @php
        $logoPath = $logo ? storage_path('app/public/' . $logo) : null;
    @endphp

    @if ($logoPath && file_exists($logoPath))
        <img src="{{ $logoPath }}" alt="Logo">
    @endif
</div>
```

**Alasan perubahan:**

- Cek keberadaan file sebelum menampilkan (mencegah error 404)
- Konsisten dengan file `penjualan.blade.php` yang sudah benar

## Bagaimana Image Upload & Display Bekerja

### Upload Process

1. User upload gambar melalui form Filament
2. Filament `FileUpload` component memproses file:
    - Validasi format (harus image)
    - Menyimpan ke `storage/app/public/{directory}/`
    - Menyimpan path relatif ke database (misal: `products/gambar-abc123.jpg`)

### Display Process

1. **Di Filament Table/Form (Input):**

    ```php
    Tables\Columns\ImageColumn::make('image')
        ->disk('public')  // Menggunakan public disk
    ```

    Filament otomatis membuat URL menggunakan `Storage::disk('public')->url()`

2. **Di Livewire/Blade View:**

    ```blade
    <img src="{{ Storage::disk('public')->url($item->image) }}" alt="">
    {{-- atau --}}
    <img src="{{ asset('storage/' . $item->image) }}" alt="">
    ```

3. **Di PDF Views:**
    ```blade
    @php
        $logoPath = $logo ? storage_path('app/public/' . $logo) : null;
    @endphp
    @if ($logoPath && file_exists($logoPath))
        <img src="{{ $logoPath }}" alt="Logo">
    @endif
    ```
    Gunakan `storage_path()` untuk dompdf karena dompdf membutuhkan absolute path

## Best Practices untuk Image Upload

1. **Selalu spesifikkan disk dan directory:**

    ```php
    FileUpload::make('image')
        ->disk('public')
        ->directory('products')
        ->visibility('public')
    ```

2. **Selalu gunakan salvagePercentage(100):**

    ```php
    ->salvagePercentage(100)  // Mencegah file loss
    ```

3. **Di Filament Table, selalu spesifikkan disk:**

    ```php
    Tables\Columns\ImageColumn::make('image')
        ->disk('public')
    ```

4. **Untuk menampilkan di Blade View:**

    ```blade
    {{-- Untuk web display (symlink) --}}
    <img src="{{ Storage::disk('public')->url($item->image) }}" alt="">
    {{-- atau --}}
    <img src="{{ asset('storage/' . $item->image) }}" alt="">

    {{-- Untuk PDF (dompdf) --}}
    @if ($logoPath && file_exists($logoPath))
        <img src="{{ $logoPath }}" alt="">
    @endif
    ```

5. **Selalu cek keberadaan file sebelum menampilkan di PDF:**
    ```blade
    @php
        $logoPath = $logo ? storage_path('app/public/' . $logo) : null;
    @endphp
    @if ($logoPath && file_exists($logoPath))
        <img src="{{ $logoPath }}" alt="">
    @endif
    ```

## Verifikasi Perbaikan

Untuk memastikan perbaikan bekerja dengan baik:

### 1. Cek Symbolic Link

```bash
php artisan storage:link
```

Pastikan folder `public/storage` exist dan merupakan symlink ke `storage/app/public`

### 2. Test Upload Gambar

- Upload produk baru dengan gambar
- Upload rental item dengan gambar
- Upload icon metode pembayaran
- Upload logo toko
- Verifikasi gambar tampil dengan benar di table dan form

### 3. Test Display

- Periksa halaman Produk, Rental Items, Payment Methods, Setting
- Verifiksa halaman POS - gambar produk tampil
- Generate PDF report dan pastikan logo tampil

### 4. Check Database

```sql
SELECT id, name, image FROM products LIMIT 5;
```

Pastikan path yang disimpan konsisten (misal: `products/nama-file.jpg`)

## Kesimpulan

Masalah terjadi karena:

1. **Inkonsistensi konfigurasi FileUpload** - tidak semua menggunakan disk/visibility/salvagePercentage
2. **Tidak ada validasi file sebelum menampilkan** di beberapa view
3. **Tidak ada standardisasi directory** untuk setiap jenis file

Solusi yang diterapkan:

1. ✅ Menambahkan `disk('public')`, `visibility('public')`, `salvagePercentage(100)` ke semua FileUpload
2. ✅ Standardisasi directory untuk setiap jenis file
3. ✅ Menambahkan validasi file_exists() di PDF views
4. ✅ Menambahkan disk specification ke semua ImageColumn

Dengan perbaikan ini, image upload dan display seharusnya berfungsi dengan baik di seluruh aplikasi.
