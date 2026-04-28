<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $totalTransaksi = Transaksi::whereDate('created_at', $today)->count();

        $totalPendapatan = Transaksi::whereDate('created_at', $today)->sum('total');

        $totalResto = Transaksi::where('layanan', 'Resto')
            ->whereDate('created_at', $today)
            ->count();

        $totalHomestay = Transaksi::where('layanan', 'Homestay')
            ->whereDate('created_at', $today)
            ->count();

        $totalWedding = Transaksi::where('layanan', 'Wedding')
            ->whereDate('created_at', $today)
            ->count();

        $transaksiTerbaru = Transaksi::latest()->take(5)->get();

        return view('kasir.dashboard', compact(
            'totalTransaksi',
            'totalPendapatan',
            'totalResto',
            'totalHomestay',
            'totalWedding',
            'transaksiTerbaru'
        ));
    }
}