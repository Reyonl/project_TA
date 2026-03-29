<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'id_cart';

    protected $fillable = [
        'id_customer',
        'id_produk',
        'id_desain',
        'quantity',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
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
