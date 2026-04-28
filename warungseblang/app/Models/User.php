<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;
public $incrementing = true;
protected $keyType = 'int';

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

    public function getAuthIdentifierName()
    {
        return 'id_user';
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}