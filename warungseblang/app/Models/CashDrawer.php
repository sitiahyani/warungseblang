<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashDrawer extends Model
{
    protected $table = 'cash_drawer';

    protected $primaryKey = 'id_drawer';

    public $timestamps = false;
    

    protected $fillable = [
        'id_user',
        'id_shift',
        'tanggal',
        'cash_awal',
        'cash_akhir',
        'selisih'
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'id_shift', 'id_shift');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}