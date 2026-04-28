<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pelanggan;
use App\Models\User;
use App\Models\Pajak;
use App\Models\Diskon;
use App\Models\HistoriPembayaranHutang;

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
        'id_pelanggan',
        'id_tipe',
        'id_kategori',
        'id_pajak',
        'id_diskon',
        'id_shift',
        'tanggal',
        'nama_pelanggan',
        'keterangan',
        'total',
        'bayar',
        'sisa_hutang',
        'metode_bayar',
        'status',
        'sumber_transaksi',
        'tanggal_acara',
        'jam_acara',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'tanggal_acara' => 'date',
        'total' => 'integer',
        'bayar' => 'integer',
        'sisa_hutang' => 'integer'
    ];

    // ================= STATUS =================
    const STATUS_LUNAS = 'lunas';
    const STATUS_BELUM = 'belum';
    const STATUS_DP = 'dp';

    // ================= METODE =================
    const METODE_TUNAI = 'tunai';
    const METODE_DEBIT = 'debit';
    const METODE_KREDIT = 'kredit';
    const METODE_QRIS = 'qris';
    const METODE_EWALLET = 'e_wallet';
    const METODE_TRANSFER = 'transfer';

    // ================= SUMBER =================
    const SUMBER_KASIR = 'kasir';
    const SUMBER_PELAYAN = 'pelayan';
    const SUMBER_WEDDING = 'wedding';
    const SUMBER_HOMESTAY = 'homestay';

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function karyawanRel(){
        return $this->belongsTo(\App\Models\Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function tipeRel(){
        return $this->belongsTo(\App\Models\Tipe::class, 'id_tipe', 'id_tipe');
    }

    public function userRel(){
        return $this->belongsTo(\App\Models\User::class, 'id_user');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function kategoriRel()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function detailLayanan()
    {
        return $this->hasMany(DetailPenjualanLayanan::class, 'id_penjualan', 'id_penjualan');
    }

    // ================= KHUSUS CATAT PESANAN =================
    public function catatPesananPelayan($keterangan = null)
    {
        $this->status = self::STATUS_BELUM;
        $this->bayar = 0;
        $this->sisa_hutang = $this->total;
        $this->metode_bayar = null; // belum bayar
        $this->keterangan = $keterangan;

        return $this->save();
    }

    public function pelangganRel()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function pajak()
    {
        return $this->belongsTo(Pajak::class, 'id_pajak', 'id_pajak');
    }

    public function diskon()
    {
        return $this->belongsTo(Diskon::class, 'id_diskon', 'id_diskon');
    }

    public function details()
    {
        return $this->hasMany(
            \App\Models\DetailPenjualan::class,
            'id_penjualan',
            'id_penjualan'
        );
    }

    public function historiPembayaran()
    {
        return $this->hasMany(
            HistoriPembayaranHutang::class,
            'id_penjualan',
            'id_penjualan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE KODE
    |--------------------------------------------------------------------------
    */

    public static function generateKodeTransaksi()
    {
        $date = now()->format('Ymd');
        $prefix = 'INV';

        $last = self::whereDate('tanggal', now()->toDateString())->count();

        return $prefix . '-' . $date . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', now()->toDateString());
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePesananPelayan($query)
    {
        return $query->where('status', self::STATUS_BELUM)
            ->where('sumber_transaksi', self::SUMBER_PELAYAN);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isLunas()
    {
        return $this->sisa_hutang <= 0;
    }

    public function updateStatusPembayaran($bayar, $metode = null)
    {
        $this->bayar = $bayar;
        $this->sisa_hutang = max($this->total - $bayar, 0);

        $this->status = $this->sisa_hutang <= 0
            ? self::STATUS_LUNAS
            : self::STATUS_DP;

        if ($metode) {
            $this->metode_bayar = $metode;
        }

        return $this->save();
    }

    public function getTotalBersihAttribute()
        {
            return $this->total - $this->nilai_pajak;
        }
}