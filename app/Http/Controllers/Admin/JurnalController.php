<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JurnalUmum;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ArrayExport;
use Maatwebsite\Excel\Facades\Excel;


class JurnalController extends Controller
{
    public function index(Request $request)
{
    $query = \App\Models\JurnalUmum::with('details');

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [
            $request->tanggal_awal,
            $request->tanggal_akhir
        ]);
    }

    $jurnals = $query->orderBy('tanggal','asc')->get();

    $totalDebit = 0;
    $totalKredit = 0;

    foreach ($jurnals as $jurnal) {
        foreach ($jurnal->details as $detail) {
            $totalDebit += $detail->debit;
            $totalKredit += $detail->kredit;
        }
    }

    return view('admin.jurnal.index', compact(
        'jurnals',
        'totalDebit',
        'totalKredit'
    ));
}
public function exportPdf(Request $request)
{
    $query = \App\Models\JurnalUmum::with('details');

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [
            $request->tanggal_awal,
            $request->tanggal_akhir
        ]);
    }

    $jurnals = $query->orderBy('tanggal','asc')->get();

    $totalDebit = 0;
    $totalKredit = 0;

    foreach ($jurnals as $jurnal) {
        foreach ($jurnal->details as $detail) {
            $totalDebit += $detail->debit;
            $totalKredit += $detail->kredit;
        }
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'admin.jurnal.pdf',
        compact('jurnals','totalDebit','totalKredit')
    )->setPaper('a4','portrait');

    return $pdf->download('jurnal-umum.pdf');
}
public function exportExcel(Request $request)
{
    $query = \App\Models\JurnalUmum::with('details');

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [
            $request->tanggal_awal,
            $request->tanggal_akhir
        ]);
    }

    $jurnals = $query->orderBy('tanggal','asc')->get();

    $data = [];

    foreach ($jurnals as $jurnal) {
        foreach ($jurnal->details as $detail) {
            $data[] = [
                'Tanggal' => $jurnal->tanggal,
                'Keterangan' => $jurnal->keterangan,
                'Debit' => $detail->debit,
                'Kredit' => $detail->kredit,
            ];
        }
    }

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\ArrayExport($data),
        'jurnal-umum.xlsx'
    );
}

}