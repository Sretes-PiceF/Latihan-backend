<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory;

    protected $table = 'admin';
    public $timestamps = true;

    protected $fillable = [
        'nama',
        'alamat',
        'no_telpn'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'admin_id');
    }
}
