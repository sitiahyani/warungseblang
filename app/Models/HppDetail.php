<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppDetail extends Model
{
    protected $table = 'hpp_detail';
    protected $primaryKey = 'id_detail';

    public $timestamps = false;

    protected $fillable = [
        'id_hpp',
        'nama_biaya',
        'nominal'
    ];

    public function hpp()
    {
        return $this->belongsTo(HppProduksi::class, 'id_hpp', 'id_hpp');
    }
}