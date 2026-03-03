<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
protected $table = 'pembelian';
protected $primaryKey = 'id_pembelian';
public $timestamps = false;

protected $fillable = [
    'tanggal',
    'id_supplier',
    'total',
    'metode_bayar',
    'status'
];

public function supplier()
{
    return $this->belongsTo(
        \App\Models\Supplier::class,
        'id_supplier',
        'id_supplier'
    );
}

public function details()
{
    return $this->hasMany(
        \App\Models\DetailPembelian::class,
        'id_pembelian',
        'id_pembelian'
    );
}
}