<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pajak extends Model
{
    protected $table = 'pajak';
    protected $primaryKey = 'id_pajak';
    public $timestamps = false;
    protected $fillable = [
        'nama_pajak',
        'tipe_pajak',
        'nilai_pajak',
        'status'
    ];
    protected $casts = [
        'nilai_pajak' => 'float'
    ];

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */
    const STATUS_AKTIF = 'aktif';
    const STATUS_NONAKTIF = 'nonaktif';

    /*
    |--------------------------------------------------------------------------
    | CEK STATUS
    |--------------------------------------------------------------------------
    */
    public function isAktif()
    {
        return $this->status === self::STATUS_AKTIF;
    }

    /*
    |--------------------------------------------------------------------------
    | GET PAJAK AKTIF
    |--------------------------------------------------------------------------
    */
    public static function getAktif()
    {
        return self::where('status', self::STATUS_AKTIF)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | HITUNG PAJAK
    |--------------------------------------------------------------------------
    */
    public function hitungPajak($subtotal)
    {
        if ($this->tipe_pajak === 'persen') {
            return ($subtotal * $this->nilai_pajak) / 100;
        }
        return $this->nilai_pajak;
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL SETELAH PAJAK
    |--------------------------------------------------------------------------
    */
    public function hitungTotal($subtotal)
    {
        return $subtotal + $this->hitungPajak($subtotal);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT NILAI
    |--------------------------------------------------------------------------
    */
    public function getNilaiFormatAttribute()
    {
        if ($this->tipe_pajak == 'persen') {
            return $this->nilai_pajak.'%';
        }
        return 'Rp '.number_format($this->nilai_pajak,0,',','.');
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT MODEL
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();
        /*
        Validasi nilai pajak
        */
        static::saving(function ($pajak) {
            if ($pajak->tipe_pajak == 'persen') {
                if ($pajak->nilai_pajak < 0 || $pajak->nilai_pajak > 100) {
                    throw new \Exception('Nilai pajak harus 0 - 100%');
                }
            }
        });
    }
}