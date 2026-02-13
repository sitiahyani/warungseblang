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


}