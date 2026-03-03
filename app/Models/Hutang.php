<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hutang extends Model
{
    use HasFactory;

    protected $table = 'hutang';
    protected $primaryKey = 'id_hutang';

    protected $fillable = [
        'id_pembelian',
        'id_supplier',
        'total',
        'sisa',
        'status'
    ];

    public $timestamps = false; 
    // Ubah jadi true kalau tabel hutang ada created_at & updated_at

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    // Relasi ke Pembelian
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'id_pembelian', 'id_pembelian');
    }

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }
    public function pembayaran()
{
    return $this->hasMany(
        PembayaranHutang::class,
        'id_hutang',
        'id_hutang'
    );
}

    /*
    |--------------------------------------------------------------------------
    | HELPER FUNCTION (Optional tapi bagus)
    |--------------------------------------------------------------------------
    */

    // Cek apakah lunas
    public function isLunas()
    {
        return $this->sisa <= 0;
    }

    // Update status otomatis
    public function updateStatus()
    {
        $this->status = $this->sisa <= 0 ? 'lunas' : 'belum';
        $this->save();
    }
}