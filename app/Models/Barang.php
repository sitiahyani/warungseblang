<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    public $timestamps = false;
    public $incrementing = true;
protected $keyType = 'int';



    protected $fillable = [
        'id_tipe',
        'id_kategori',
        'kode_barang',
        'nama_barang',
        'satuan',
        'harga_jual',
        'hpp',
        'gambar',
        'keterangan',
        'status',
        'stok'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class, 'id_tipe', 'id_tipe');
    }
    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'barang_id');
    }


}