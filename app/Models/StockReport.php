<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'report_date',
        'total_items',
        'total_cost_value',
        'total_selling_value',
        'path_file',
    ];
    protected $casts = [
        'report_date' => 'date',
        'total_items' => 'integer',
        'total_cost_value' => 'integer',
        'total_selling_value' => 'integer',
    ];
}
