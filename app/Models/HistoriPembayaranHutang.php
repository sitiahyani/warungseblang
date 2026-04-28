<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriPembayaranHutang extends Model
{
    protected $table = 'histori_pembayaran_hutang';
    protected $primaryKey = 'id_histori';

    protected $fillable = [
        'id_penjualan',
        'tanggal_bayar',
        'jumlah_bayar',
        'sisa_hutang'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }
}