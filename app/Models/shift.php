<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    protected $table = 'shift';
    protected $primaryKey = 'id_shift';
    public $timestamps = false;

    protected $fillable = [
        'id_karyawan',
        'kode',
        'nama_shift',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'jumlah',
        'keterangan'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class,'id_karyawan','id_karyawan');
    }

    public function getStatusAktifAttribute()
    {
        $now = Carbon::now()->format('H:i:s');

        if ($now >= $this->waktu_mulai && $now <= $this->waktu_selesai) {
            return 'buka';
        }

        return 'tutup';
    }

    public function getWaktuAttribute()
    {
        return date('H.i', strtotime($this->waktu_mulai)) .
               ' - ' .
               date('H.i', strtotime($this->waktu_selesai));
    }
}