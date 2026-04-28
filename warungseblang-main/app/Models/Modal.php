<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modal extends Model
{
    protected $table = 'modal';
    protected $primaryKey = 'id_modal';

    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'jumlah',
        'jenis',
        'keterangan'
    ];
}