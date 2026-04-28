<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LaporanPenjualanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Penjualan;
use App\Models\Kategori;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with([
            'pelangganRel',
            'kategoriRel',
            'details',
            'detailLayanan'
        ]);

        // ================= FILTER TANGGAL =================
        if ($request->tanggal) {
            $tgl = explode(' - ', $request->tanggal);

            if (count($tgl) == 2) {
                $query->whereBetween('tanggal', [
                    $tgl[0] . ' 00:00:00',
                    $tgl[1] . ' 23:59:59'
                ]);
            }
        }

        // ================= FILTER KATEGORI =================
        if ($request->kategori) {
            $query->where('id_kategori', $request->kategori);
        }

        // ================= FILTER METODE =================
        if ($request->metode_bayar) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $penjualan = $query->orderBy('tanggal', 'desc')->get();
        $kategori = Kategori::all();

        return view('admin.laporan_transaksi.laporan_penjualan', compact('penjualan', 'kategori'));
    }

    // ================= PDF =================
    public function exportPdf(Request $request)
    {
        $query = Penjualan::with(['pelangganRel','kategoriRel']);

        if ($request->tanggal) {
            $tgl = explode(' - ', $request->tanggal);
            $query->whereBetween('tanggal', [
                $tgl[0].' 00:00:00',
                $tgl[1].' 23:59:59'
            ]);
        }

        if ($request->kategori) {
            $query->where('id_kategori', $request->kategori);
        }

        if ($request->metode_bayar) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $data = $query->orderBy('tanggal','desc')->get();

        $pdf = Pdf::loadView('admin.laporan_transaksi.pdf_penjualan', [
            'penjualan' => $data
        ]);

        return $pdf->download('laporan_penjualan.pdf');
    }

    // ================= EXCEL =================
    public function exportExcel(Request $request)
    {
        $query = Penjualan::with(['pelangganRel','kategoriRel']);

        if ($request->tanggal) {
            $tgl = explode(' - ', $request->tanggal);
            $query->whereBetween('tanggal', [
                $tgl[0].' 00:00:00',
                $tgl[1].' 23:59:59'
            ]);
        }

        if ($request->kategori) {
            $query->where('id_kategori', $request->kategori);
        }

        if ($request->metode_bayar) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $data = $query->orderBy('tanggal','desc')->get();

        return Excel::download(
            new LaporanPenjualanExport($data),
            'laporan_penjualan.xlsx'
        );
    }
}