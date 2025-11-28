<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    /** @use HasFactory<\Database\Factories\PelangganFactory> */
    use HasFactory;
    protected $table = 'pelanggan';
    public $timestamps = true;
    protected $fillable = [
        'nama',
        'alamat',
        'no_telpn'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'pelanggan_id');
    }

    public function pelanggandata()
    {
        return $this->hasMany(PelangganData::class, 'pelanggan_id');
    }

    public function penyewaan()
    {
        return $this->hasMany(Penyewaan::class, 'pelanggan_id');
    }
}
