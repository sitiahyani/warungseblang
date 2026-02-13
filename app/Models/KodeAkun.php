<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeAkun extends Model
{
    protected $table = 'akun';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'jenis_akun'
    ];
}