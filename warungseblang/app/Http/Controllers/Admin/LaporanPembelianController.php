<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArrayExport;

class LaporanPembelianController extends Controller
{
    public function index(Request $request)
{
    $query = \App\Models\Pembelian::with(['supplier','details.bahan']);

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [
            $request->tanggal_awal . ' 00:00:00',
            $request->tanggal_akhir . ' 23:59:59'
        ]);
    }

    $pembelians = $query->orderBy('tanggal','asc')->get();

    $total = $pembelians->sum('total');

    return view('admin.laporan.pembelian.index', compact(
        'pembelians',
        'total'
    ));
}
public function exportPdf(Request $request)
{
    $query = \App\Models\Pembelian::with(['supplier','details.bahan']);

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [
            $request->tanggal_awal . ' 00:00:00',
            $request->tanggal_akhir . ' 23:59:59'
        ]);
    }

    $pembelians = $query->orderBy('tanggal','asc')->get();
    $total = $pembelians->sum('total');

    $pdf = Pdf::loadView(
        'admin.laporan.pembelian.pdf',
        compact('pembelians','total')
    )->setPaper('a4','portrait');

    return $pdf->stream('laporan-pembelian.pdf'); // <-- stream biar bisa dicetak dulu
}
public function exportExcel(Request $request)
{
    $query = \App\Models\Pembelian::with(['supplier','details.bahan']);

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [
            $request->tanggal_awal . ' 00:00:00',
            $request->tanggal_akhir . ' 23:59:59'
        ]);
    }

    $pembelians = $query->orderBy('tanggal','asc')->get();

    $data = [];

    foreach ($pembelians as $pembelian) {
        foreach ($pembelian->details as $detail) {
            $data[] = [
                'Tanggal'   => $pembelian->tanggal,
                'Supplier'  => $pembelian->supplier->nama_supplier ?? '-',
                'Bahan'     => $detail->bahan->nama_bahan ?? '-',
                'Qty'       => $detail->qty,
                'Harga'     => $detail->harga,
                'Subtotal'  => $detail->subtotal,
            ];
        }
    }

    return Excel::download(
        new ArrayExport($data),
        'laporan-pembelian.xlsx'
    );
}
}