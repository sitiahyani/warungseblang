<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'id_karyawan',
    ];

    protected $hidden = [
        'password',
    ];
    public function karyawan()
{
    return $this->belongsTo(Karyawan::class);
}

}