<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashDrawer;
use App\Exports\LaporanShiftExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = CashDrawer::with(['shift', 'user']);

        // FILTER TANGGAL
        if ($request->tanggal) {
            [$start, $end] = explode(' - ', $request->tanggal);

            $query->whereBetween('tanggal', [$start, $end]);
        }

        // FILTER NAMA SHIFT (dropdown)
        if ($request->nama_shift) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('id_shift', $request->nama_shift);
            });
        }

        // FILTER KASIR (dropdown)
        if ($request->kasir) {
            $query->where('id_user', $request->kasir);
        }

        $laporan = $query->orderBy('id_drawer', 'desc')->get();

        // ambil data dropdown
        $shiftList = \App\Models\Shift::all();
        $kasirList = \App\Models\User::where('role', 'kasir')->get();

        return view('admin.laporan_transaksi.laporan_shift', compact(
            'laporan',
            'shiftList',
            'kasirList'
        ));
    }

    public function exportPdf()
    {
        $laporan = CashDrawer::with(['shift', 'user'])->get();

        $pdf = Pdf::loadView('admin.laporan_transaksi.pdf_shift', compact('laporan'));

        return $pdf->download('laporan-shift.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new LaporanShiftExport, 'laporan-shift.xlsx');
    }
}