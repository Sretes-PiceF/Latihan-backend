<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyewaan extends Model
{
    protected $table = 'penyewaan';
    protected $fillable = [
        'pelanggan_id',
        'product_id',
        'tglsewa',
        'tglkembali',
        'status_pembayaran',
        'status_kembali',
        'total_harga'
    ];


    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function penyewaanDetail()
    {
        return $this->hasMany(PenyewaanDetail::class, 'penyewaan_id');
    }
}
