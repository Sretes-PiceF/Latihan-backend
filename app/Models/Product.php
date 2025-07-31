<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $keyType = 'string';
    protected $fillable = [
        'product_id',
        'product_name',
        'product_stock',
        'product_price'
    ];
}
