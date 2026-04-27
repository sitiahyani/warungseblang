<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    protected $table = 'stok_opname';
    protected $primaryKey = 'id_opname';

    protected $fillable = [
        'id_bahan',
        'tanggal',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan',
        'status'
    ];

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan', 'id_bahan');
    }
}