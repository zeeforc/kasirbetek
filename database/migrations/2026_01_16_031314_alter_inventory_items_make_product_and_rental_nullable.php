<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();

            $table->dropForeign(['rental_item_id']);
            $table->unsignedBigInteger('rental_item_id')->nullable()->change();
            $table->foreign('rental_item_id')->references('id')->on('rental_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            $table->dropForeign(['rental_item_id']);
            $table->unsignedBigInteger('rental_item_id')->nullable(false)->change();
            $table->foreign('rental_item_id')->references('id')->on('rental_items')->cascadeOnDelete();
        });
    }
};
