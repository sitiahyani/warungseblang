<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualanLayanan extends Model
{
    protected $table = 'detail_penjualan_layanan';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_penjualan',
        'id_layanan',
        'durasi',
        'harga',
        'tanggal_checkin',
        'tanggal_checkout',
        'subtotal'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }
}