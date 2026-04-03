<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';
    protected $primaryKey = 'id_order_detail';

    protected $fillable = [
        'id_order',
        'id_produk',
        'id_desain',
        'quantity',
        'harga_produk',
        'harga_desain',
        'subtotal',
        'status_desain',
        'catatan_admin',
        'tipe_proses',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function desain()
    {
        return $this->belongsTo(Desain::class, 'id_desain', 'id_desain');
    }
}
