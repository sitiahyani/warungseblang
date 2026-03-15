<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    public $timestamps = false;

    protected $fillable = [
        'kode_transaksi',
        'id_user',
        'id_karyawan',
        'id_shift',
        'id_pelanggan',
        'tanggal',
        'id_tipe',
        'total',
        'bayar',
        'sisa_bayar',
        'id_pajak',
        'id_diskon',
        'metode_bayar',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'total' => 'integer',
        'bayar' => 'integer',
        'sisa_bayar' => 'integer'
    ];

    // Status pembayaran
    const STATUS_LUNAS = 'lunas';
    const STATUS_BELUM = 'belum';

    // Metode pembayaran
    const METODE_TUNAI = 'tunai';
    const METODE_DEBIT = 'debit';
    const METODE_KREDIT = 'kredit';
    const METODE_QRIS = 'qris';
    const METODE_EWALLET = 'e_wallet';
    const METODE_TRANSFER = 'transfer';

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Relasi ke User (Kasir)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    // Relasi ke Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    // Relasi ke Pajak
    public function pajak()
    {
        return $this->belongsTo(Pajak::class, 'id_pajak', 'id_pajak');
    }

    // Relasi ke Detail Penjualan
    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan', 'id_penjualan');
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE KODE TRANSAKSI
    |--------------------------------------------------------------------------
    */

    public static function generateKodeTransaksi()
    {
        $date = date('Ymd');
        $prefix = 'INV';

        $last = self::whereDate('tanggal', date('Y-m-d'))->count();

        $number = $last + 1;

        return $prefix . '-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE QUERY
    |--------------------------------------------------------------------------
    */

    // Transaksi hari ini
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', date('Y-m-d'));
    }

    // Berdasarkan status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    // Cek apakah lunas
    public function isLunas()
    {
        return $this->sisa_bayar <= 0;
    }

    // Update pembayaran
    public function updateStatusPembayaran($bayar, $metode = null)
    {
        $this->bayar = $bayar;
        $this->sisa_bayar = $this->total - $bayar;

        if ($this->sisa_bayar <= 0) {
            $this->status = self::STATUS_LUNAS;
            $this->sisa_bayar = 0;
        } else {
            $this->status = self::STATUS_BELUM;
        }

        if ($metode) {
            $this->metode_bayar = $metode;
        }

        return $this->save();
    }
}