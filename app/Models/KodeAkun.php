<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeAkun extends Model
{
    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'jenis_akun'
    ];
}