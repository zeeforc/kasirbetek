<?php

use App\Http\Controllers\StockReportController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

// Route::get('/transactions', function () {
//     return view('transactions');
// });

Route::get('/test-images', function () {
    $products = Product::whereNotNull('image')->limit(15)->get();
    $output = '';
    foreach ($products as $p) {
        $path = storage_path('app/public/' . $p->image);
        $exists = file_exists($path) ? 'YES' : 'NO';
        $output .= "ID: {$p->id} | {$p->name} | Path: {$p->image} | Exists: {$exists}\n";
    }
    return response($output, 200)->header('Content-Type', 'text/plain');
});

Route::middleware(['web'])->group(function () {
    Route::get('stock-reports/{stock_report}/download', [StockReportController::class, 'download'])
        ->name('stock-reports.download');

    Route::get('reports/{report}/download', [ReportController::class, 'download'])
        ->name('reports.download');
});
