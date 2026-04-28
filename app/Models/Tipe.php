<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipe extends Model
{
    protected $table = 'tipe';
    protected $primaryKey = 'id_tipe';

    protected $fillable = ['nama_tipe'];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_tipe', 'id_tipe');
    }
}