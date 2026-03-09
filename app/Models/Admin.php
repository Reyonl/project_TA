<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admins';
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nama_admin',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function templates()
    {
        return $this->hasMany(Template::class, 'id_admin', 'id_admin');
    }
}
