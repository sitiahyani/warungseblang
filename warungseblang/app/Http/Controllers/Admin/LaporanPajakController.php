<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Pajak;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPajakExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPajakController extends Controller
{
    private function getData($request)
    {
        $query = Penjualan::with(['kategoriRel']);

        // FILTER TANGGAL
        if ($request->tanggal) {
            $tgl = explode(' - ', $request->tanggal);
            if (count($tgl) == 2) {
                $query->whereBetween('tanggal', [
                    $tgl[0].' 00:00:00',
                    $tgl[1].' 23:59:59'
                ]);
            }
        }

        // KHUSUS RESTO
        $query->whereHas('kategoriRel', function ($q) {
            $q->where('nama_kategori', 'restoran');
        });

        $data = $query->orderBy('tanggal','desc')->get();

        return $data;
    }

    // ================= VIEW =================
    public function index(Request $request)
    {
        $data = $this->getData($request);

        return view('admin.laporan_transaksi.laporan_pajak', compact('data'));
    }

    // ================= PDF =================
    public function exportPdf(Request $request)
    {
        $data = $this->getData($request);

        $pdf = Pdf::loadView('admin.laporan_transaksi.pdf_pajak', [
            'data' => $data
        ]);

        return $pdf->download('laporan-pajak.pdf');
    }

    // ================= EXCEL =================
    public function exportExcel(Request $request)
    {
        $data = $this->getData($request);

        return Excel::download(new LaporanPajakExport($data, $request->persen ?? 0),'laporan-pajak.xlsx');
    }
}