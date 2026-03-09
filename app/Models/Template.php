<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates';
    protected $primaryKey = 'id_template';

    protected $fillable = [
        'id_admin',
        'nama_template',
        'file_template',
        'kategori',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function desains()
    {
        return $this->hasMany(Desain::class, 'id_template', 'id_template');
    }
}
