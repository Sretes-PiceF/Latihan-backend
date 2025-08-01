<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriesProduct extends Model
{
    /** @use HasFactory<\Database\Factories\CategoriesProductFactory> */
    use HasFactory;
    protected $table = 'categories_products';
    protected $primaryKey = 'categories_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'categories_id',
        'categories_nama'
    ];
}
