<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use DB;

class LaporanStokController extends Controller
{
    public function index()
    {
        $barangs = Barang::withSum('detailPembelian as stok_masuk', 'qty')
            ->withSum('detailPenjualan as stok_keluar', 'qty')
            ->get();

        return view('admin.laporan.stok.index', compact('barangs'));
    }
}