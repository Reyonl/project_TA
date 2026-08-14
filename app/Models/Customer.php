<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;
    protected $table = 'customers';
    protected $primaryKey = 'id_customer';

    protected $fillable = [
        'nama_customer',
        'email',
        'password',
        'no_hp',
        'alamat',
    ];

    protected $hidden = [
        'password',
    ];

    public function desains()
    {
        return $this->hasMany(Desain::class, 'id_customer', 'id_customer');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_customer', 'id_customer');
    }
}
