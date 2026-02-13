<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KodeAkun;
use Illuminate\Http\Request;

class KodeAkunController extends Controller
{
    public function index()
{
    $akun = \App\Models\KodeAkun::orderBy('kode_akun')->get();
    return view('admin.kode_akun.index', compact('akun'));
}

}