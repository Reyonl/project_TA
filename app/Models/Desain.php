<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desain extends Model
{
    protected $table = 'desains';
    protected $primaryKey = 'id_desain';

    protected $fillable = [
        'id_customer',
        'id_template',
        'file_desain',
        'file_desain_belakang',
        'file_desain_kiri',
        'file_desain_kanan',
        'harga_desain',
        'warna_baju',
        'canvas_front',
        'canvas_back',
        'canvas_left',
        'canvas_right',
        'raw_assets',
        'detail_sablon',
    ];

    protected $casts = [
        'raw_assets' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
    }

    public function template()
    {
        return $this->belongsTo(Template::class, 'id_template', 'id_template');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'id_desain', 'id_desain');
    }
}
