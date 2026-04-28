<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan'; 
    protected $primaryKey = 'id_layanan';
    public $timestamps = false;

    protected $fillable = [
        'id_tipe',
        'kode_layanan',
        'nama_layanan',
        'id_kategori',
        'harga',
        'deskripsi',
        'status'
    ];

    // relasi ke tabel tipe
    public function tipe()
    {
        return $this->belongsTo(Tipe::class, 'id_tipe', 'id_tipe');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}