<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelangganData extends Model
{
    protected $fillable = [
        'pelanggan_id',
        'jenis',
        'file'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
}
