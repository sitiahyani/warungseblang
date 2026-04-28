<?php
// app/Models/DetailPenjualan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan_barang';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_penjualan',
        'id_barang',
        'qty',
        'harga',
        'subtotal'
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga' => 'integer',
        'subtotal' => 'integer'
    ];

    /**
     * Relasi ke Penjualan
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    /**
     * Relasi ke Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    /**
     * Hitung subtotal otomatis
     */
    public static function hitungSubtotal($qty, $harga)
    {
        return $qty * $harga;
    }
}