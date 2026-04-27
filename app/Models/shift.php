<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shift';
    protected $primaryKey = 'id_shift';

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama_shift',
        'id_karyawan', // 🔥 WAJIB ADA (ini penyebab error kamu tadi)
        'waktu_mulai',
        'waktu_selesai',
        'jumlah',
        'status',
        'keterangan'
    ];

    // 🔗 RELASI KE KARYAWAN
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    // 🔗 RELASI CASH DRAWER
    public function cashDrawers()
    {
        return $this->hasMany(CashDrawer::class, 'id_shift', 'id_shift');
    }
}