<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rental_items', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('chair');
            $table->string('name');
            $table->string('image')->nullable();
            $table->integer('cost_price');
            $table->integer('stock')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rental_items');
    }
};
