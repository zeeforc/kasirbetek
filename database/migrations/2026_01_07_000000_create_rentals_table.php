<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->string('renter_name')->nullable();
            $table->string('renter_contact')->nullable();
            $table->string('type'); // chair|room|service
            $table->unsignedBigInteger('resource_id')->nullable(); // FK to products or rooms
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('duration')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('deposit', 15, 2)->nullable()->default(0);
            $table->string('status')->default('booked'); // booked|checked_out|checked_in|cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rentals');
    }
};
