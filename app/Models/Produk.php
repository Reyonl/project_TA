<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produks';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk',
        'jenis_produk',
        'harga_dasar',
        'deskripsi',
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'id_produk', 'id_produk');
    }
}
