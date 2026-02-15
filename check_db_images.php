<?php
$products = DB::table('products')->whereNotNull('image')->limit(15)->get();
foreach ($products as $p) {
    $path = storage_path('app/public/' . $p->image);
    $exists = file_exists($path) ? 'YES' : 'NO';
    echo $p->id . ' | ' . $p->name . ' | ' . $p->image . ' | ' . $exists . PHP_EOL;
}
