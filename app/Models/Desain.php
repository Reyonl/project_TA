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
        'lebar_cm',
        'lebar_cm_belakang',
        'tinggi_cm',
        'tinggi_cm_belakang',
        'harga_desain',
        'tanggal_upload',
        'warna_baju',
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
