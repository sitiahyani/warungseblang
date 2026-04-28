<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
     protected $primaryKey = 'id_karyawan';
    public $timestamps = false; // 🔥 INI KUNCINYA
    public $incrementing = true;
    protected $keyType = 'int';

    
    protected $fillable = [
        'nama_karyawan',
        'no_hp',
        'jenis_kelamin',
        'email',
        'foto',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}