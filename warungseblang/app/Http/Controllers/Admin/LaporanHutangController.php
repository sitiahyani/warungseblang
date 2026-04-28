<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hutang;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArrayExport;

class LaporanHutangController extends Controller
{
    // =========================
    // HALAMAN LAPORAN
    // =========================
    public function index(Request $request)
    {
        $query = Hutang::with('supplier');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00',
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        $hutangs = $query->orderBy('created_at', 'asc')->get();

        $totalSisa = $hutangs->sum('sisa_hutang');

        return view('admin.laporan.hutang.index', compact('hutangs', 'totalSisa'));
    }

    // =========================
    // EXPORT PDF
    // =========================
    public function exportPdf(Request $request)
    {
        $query = Hutang::with('supplier');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00',
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        $hutangs = $query->orderBy('created_at', 'asc')->get();

        $total_hutang = $hutangs->sum('total_hutang');
        $total_sisa   = $hutangs->sum('sisa_hutang');

        $pdf = Pdf::loadView(
            'admin.laporan.hutang.pdf',
            compact('hutangs', 'total_hutang', 'total_sisa')
        )->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-hutang.pdf');
    }

    // =========================
    // EXPORT EXCEL
    // =========================
    public function exportExcel(Request $request)
    {
        $query = Hutang::with('supplier');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00',
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        $hutangs = $query->orderBy('created_at', 'asc')->get();

        $data = [];

        foreach ($hutangs as $hutang) {
            $data[] = [
                'Tanggal'        => $hutang->created_at->format('d-m-Y'),
                'Supplier'       => $hutang->supplier->nama_supplier ?? '-',
                'Total Hutang'   => $hutang->total_hutang,
                'Sisa Hutang'    => $hutang->sisa_hutang,
                'Keterangan'     => $hutang->keterangan,
            ];
        }

        return Excel::download(
            new ArrayExport($data),
            'laporan-hutang.xlsx'
        );
    }
}