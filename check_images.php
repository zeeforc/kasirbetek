<?php
require __DIR__ . '/bootstrap/app.php';

$products = \App\Models\Product::whereNotNull('image')->select('id', 'name', 'image')->get();
foreach ($products as $p) {
    $path = storage_path('app/public/' . $p->image);
    $exists = file_exists($path) ? 'EXISTS' : 'MISSING';
    echo "ID: {$p->id}, Name: {$p->name}, Image: {$p->image}, Status: {$exists}\n";
}
echo "\nTotal products with image: " . $products->count() . "\n";
