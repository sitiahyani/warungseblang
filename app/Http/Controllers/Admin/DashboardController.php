<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 🔥 TRANSAKSI HARI INI
        $totalTransaksi = DB::table('penjualan')
            ->whereDate('tanggal', $today)
            ->count();

        // 💰 PEMASUKAN HARI INI
        $totalPemasukan = DB::table('penjualan')
            ->whereDate('tanggal', $today)
            ->sum('total');

        // 📊 TRANSAKSI PER KATEGORI
        $transaksiPerKategori = DB::table('penjualan as p')
            ->join('kategori as k', 'p.id_kategori', '=', 'k.id_kategori')
            ->select('k.nama_kategori', DB::raw('COUNT(*) as total'))
            ->whereDate('p.tanggal', $today)
            ->groupBy('k.nama_kategori')
            ->get();

        // 📈 GRAFIK 7 HARI TERAKHIR
        $grafik = DB::table('penjualan')
            ->selectRaw('DATE(tanggal) as tgl, SUM(total) as total')
            ->whereBetween('tanggal', [now()->subDays(6), now()])
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        $labels = $grafik->pluck('tgl');
        $data   = $grafik->pluck('total');

        return view('admin.dashboard', compact(
            'totalTransaksi',
            'totalPemasukan',
            'transaksiPerKategori',
            'labels',
            'data'
        ));
    }
}