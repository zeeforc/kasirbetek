# 🔧 FIX NGROK ISSUES - Stock Reports & Report Download

## 📋 Masalah yang Ditemukan & Diperbaiki

### ❌ Masalah 1: Stock Reports Page Error (500)

**Penyebab:** Route untuk download stock reports dikomentar di `routes/web.php`

**Solusi:** Aktifkan route yang sudah ada

```php
// SEBELUM (dikomentar)
// Route::get('stock-reports/{stock_report}/download', [StockReportController::class, 'download'])
//     ->name('stock-reports.download');

// SESUDAH (aktif)
Route::get('stock-reports/{stock_report}/download', [StockReportController::class, 'download'])
    ->name('stock-reports.download');
```

---

### ❌ Masalah 2: Report PDF Download Error (403 Forbidden)

**Penyebab:** Menggunakan `asset('storage/' . $path)` yang tidak compatible dengan ngrok

Ketika pakai ngrok dengan `ASSET_URL=https://ngrok-url.dev`:

- `asset('storage/reports/file.pdf')` akan generate URL: `https://ngrok-url.dev/storage/reports/file.pdf`
- Tapi ngrok tidak bisa forward static files dengan benar
- Hasilnya: **403 Forbidden**

**Solusi:** Buat route khusus untuk download PDF (bukan serve via asset static)

**File yang dibuat/diubah:**

1. **Buat ReportController.php** (new file)

```php
class ReportController extends Controller {
    public function download(Report $report) {
        $path = $report->path_file;
        $fullPath = storage_path('app/public/' . ltrim($path, '/'));

        if (! file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($fullPath, $report->name . '.pdf');
    }
}
```

2. **Update routes/web.php**

```php
Route::get('reports/{report}/download', [ReportController::class, 'download'])
    ->name('reports.download');
```

3. **Update ReportResource.php**

```php
// SEBELUM
->url(fn ($record) => asset('storage/' . $record->path_file))

// SESUDAH
->url(fn ($record) => route('reports.download', $record))
```

---

## ✅ Hasil Perbaikan

| Issue                    | Status   | Solusi                |
| ------------------------ | -------- | --------------------- |
| Stock Reports 500 Error  | ✅ FIXED | Route diaktifkan      |
| Report PDF 403 Forbidden | ✅ FIXED | Route khusus download |

---

## 🎯 Kenapa Ini Penting untuk Ngrok

**Perbedaan Static Assets vs Dynamic Routes pada Ngrok:**

### ❌ Static Assets (asset() helper)

```
ASSET_URL=https://coincident-jaunita-impolite.ngrok-free.dev
asset('storage/file.pdf')
↓
https://coincident-jaunita-impolite.ngrok-free.dev/storage/file.pdf
↓
❌ Ngrok tries to forward to localhost:8000/storage/file.pdf
↓
403 Forbidden (ngrok tidak bisa handle static files via symlink)
```

### ✅ Dynamic Routes (route() helper)

```
route('reports.download', $record)
↓
https://coincident-jaunita-impolite.ngrok-free.dev/reports/{id}/download
↓
✅ Ngrok forwards to localhost:8000/reports/{id}/download
↓
✅ Laravel controller handles download properly
↓
✅ File served correctly dengan response()->download()
```

---

## 📝 Untuk Ngrok Setup di Masa Depan

**Best Practice:**

1. **Static Assets (CSS, JS, Images):** Gunakan `asset()` helper
2. **File Downloads (PDF, XLSX):** Gunakan route + controller dengan `response()->download()`
3. **User Uploads (Storage):** Gunakan symlink `public/storage` + route khusus

---

## 🔍 File yang Dimodifikasi

```
✅ routes/web.php
   - Uncommented stock-reports route
   - Added reports download route

✅ app/Http/Controllers/ReportController.php (NEW)
   - Created new controller for PDF download

✅ app/Filament/Resources/ReportResource.php
   - Changed download URL from asset() to route()
```

---

## 🧪 Testing

1. **Stock Reports Page:**
    - Buka: `https://ngrok-url.dev/admin/stock-reports`
    - Klik download button
    - ✅ Harus download .xlsx file

2. **Report Download:**
    - Buka: `https://ngrok-url.dev/admin/reports`
    - Klik download button
    - ✅ Harus download .pdf file (bukan 403 forbidden)

---

## 💡 Key Takeaway

**Jangan gunakan `asset()` untuk file downloads via ngrok.**
**Gunakan route + controller dengan `response()->download()` instead.**

Ini berlaku untuk semua file downloads (PDF, Excel, ZIP, etc).
