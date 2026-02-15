<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'image',
        'stock',
        'price',
        'cost_price',
        'is_active'
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'resource_id');
    }
}
