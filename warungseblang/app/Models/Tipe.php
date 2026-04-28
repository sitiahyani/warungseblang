<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipe extends Model
{
    protected $table = 'tipe';
    protected $primaryKey = 'id_tipe';
    public $timestamps = false;

    protected $fillable = [
        'id_kategori',
        'nama_tipe'
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_tipe', 'id_tipe');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}