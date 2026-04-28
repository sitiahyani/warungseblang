<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
      protected $table = 'diskon';
    protected $primaryKey = 'id_diskon';
    public $timestamps = false;

    protected $fillable = [
    'nama_diskon',
    'tipe_diskon',
    'nilai_diskon',
    'masa_aktif_tipe',
    'masa_aktif_nilai',
    'max_pesanan',
    'status'
];
public function penjualan()
{
    return $this->hasMany(\App\Models\Penjualan::class, 'id_diskon', 'id_diskon');
}
}