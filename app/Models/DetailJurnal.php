<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailJurnal extends Model
{
    protected $table = 'detail_jurnal';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_jurnal',
        'id_akun',
        'debit',
        'kredit'
    ];

    public function akun()
{
    return $this->belongsTo(KodeAkun::class, 'id_akun', 'id_akun');
}
 public function jurnal()
    {
        return $this->belongsTo(
            \App\Models\JurnalUmum::class,
            'id_jurnal',
            'id_jurnal'
        );
    }
}