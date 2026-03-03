<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
 protected $table = 'detail_pembelian';
protected $primaryKey = 'id_detail';
public $timestamps = false;

protected $fillable = [
    'id_pembelian',
    'id_bahan',
    'qty',
    'harga',
    'subtotal'
];
public function bahan()
{
    return $this->belongsTo(
        \App\Models\BahanBaku::class,
        'id_bahan',
        'id_bahan'
    );
}

}