<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'renter_name',
        'renter_contact',
        'type',
        'resource_id',
        'start_at',
        'end_at',
        'quantity',
        'duration',
        'price',
        'deposit',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function resource()
    {
        return $this->belongsTo(Product::class, 'resource_id');
    }

    public function rentalItem()
    {
        return $this->belongsTo(RentalItem::class, 'resource_id');
    }
}
