<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

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
