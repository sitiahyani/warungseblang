<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppProduksi extends Model
{
    protected $table = 'hpp_produksi';
    protected $primaryKey = 'id_hpp';

    public $timestamps = false; // tabel kamu tidak ada created_at

    protected $fillable = [
        'id_barang',
        'tanggal',
        'total_biaya',
        'hpp_unit',
        'status'
    ];

    // Relasi ke barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}