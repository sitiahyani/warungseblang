<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Hutang;
use App\Models\Piutang;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JurnalExport;

class LaporanController extends Controller
{
    public function jurnal(Request $request)
{
    $query = JurnalUmum::with('detail.akun');

    // FILTER TANGGAL RANGE
    if ($request->dari && $request->sampai) {
        $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
    }

    // FILTER BULAN
    if ($request->bulan) {
        $query->whereMonth('tanggal', $request->bulan);
    }

    // FILTER TAHUN
    if ($request->tahun) {
        $query->whereYear('tanggal', $request->tahun);
    }

    $data = $query->orderBy('tanggal','asc')->get();

    // EXPORT PDF
    if ($request->export == 'pdf') {
        $pdf = Pdf::loadView('admin.laporan.jurnal_pdf', compact('data','request'));
        return $pdf->download('laporan-jurnal.pdf');
    }

    // EXPORT EXCEL
    if ($request->export == 'excel') {
        return Excel::download(new JurnalExport($data), 'laporan-jurnal.xlsx');
    }

    return view('admin.laporan.jurnal', compact('data'));
}


    public function pembelian()
    {
        $data = Pembelian::with('supplier')
                ->orderBy('tanggal','desc')
                ->get();

        return view('admin.laporan.pembelian', compact('data'));
    }

   
    public function hutang()
    {
        $data = Hutang::with('supplier')
                ->orderBy('id_hutang','desc')
                ->get();

        return view('admin.laporan.hutang', compact('data'));
    }

    
}