<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyewaanDetail extends Model
{
    protected $table = 'penyewaan_detail';
    protected $fillable = [
        'penyewaan_id',
        'product_id',
        'jumlah',
        'subharga'
    ];

    public function penyewaan()
    {
        return $this->belongsTo(Penyewaan::class, 'penyewaan_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
