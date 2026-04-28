<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function resto()
    {
        return view('kasir.penjualan.resto');
    }

    public function homestay()
    {
        return view('kasir.penjualan.homestay');
    }

    public function wedding()
    {
        return view('kasir.penjualan.wedding');
    }
}