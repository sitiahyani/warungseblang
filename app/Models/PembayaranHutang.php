<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranHutang extends Model
{
    protected $table = 'pembayaran_hutang';
    protected $primaryKey = 'id_bayar';
    public $timestamps = true;

    protected $fillable = [
        'id_hutang',
        'tanggal',
        'jumlah_bayar'
    ];

    public function hutang()
    {
        return $this->belongsTo(
            Hutang::class,
            'id_hutang',
            'id_hutang'
        );
    }
}