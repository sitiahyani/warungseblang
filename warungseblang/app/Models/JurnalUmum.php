<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal';
    public $timestamps = true;

    protected $fillable = [
        'tanggal',
        'keterangan',
        'sumber',
        'ref_id'
    ];

    // 🔥 WAJIB ADA INI
    public function details()
    {
        return $this->hasMany(
            \App\Models\DetailJurnal::class,
            'id_jurnal',
            'id_jurnal'
        );
    }
}