<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'product_id',
        'rental_item_id',
        'cost_price',
        'quantity',
    ];


    public function inventory()
    {
        return $this->belongsTo(\App\Models\Inventory::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function rentalItem()
    {
        return $this->belongsTo(\App\Models\RentalItem::class);
    }
}
