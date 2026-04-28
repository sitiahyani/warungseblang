<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailJurnal;
use App\Models\KodeAkun;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ArrayExport;
use Maatwebsite\Excel\Facades\Excel;

class BukuBesarController extends Controller
{
        public function index(Request $request)
{

$query = DetailJurnal::with(['akun','jurnal']);

if($request->akun){
$query->where('id_akun',$request->akun);
}

$details = $query->get();

$akuns = KodeAkun::all();

return view('admin.buku_besar.index',compact(
'details',
'akuns'
));

}
public function exportPdf(Request $request)
{
    $query = DetailJurnal::with(['akun','jurnal']);

    if ($request->filled('akun')) {
        $query->where('id_akun', $request->akun);
    }

    $details = $query->orderBy('id_jurnal','asc')->get();

    $saldo = 0;

    foreach ($details as $d) {
        $saldo += $d->debit - $d->kredit;
        $d->saldo = $saldo;
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'admin.buku_besar.pdf',
        compact('details')
    )->setPaper('a4','portrait');

    return $pdf->stream('buku-besar.pdf');
}
public function exportExcel(Request $request)
{
    $query = DetailJurnal::with(['akun','jurnal']);

    if ($request->filled('akun')) {
        $query->where('id_akun', $request->akun);
    }

    $details = $query->orderBy('id_jurnal','asc')->get();

    $saldo = 0;
    $data = [];

    foreach ($details as $d) {

        $saldo += $d->debit - $d->kredit;

        $data[] = [
            'Tanggal' => $d->jurnal->tanggal,
            'Keterangan' => $d->jurnal->keterangan,
            'Akun' => $d->akun->nama_akun,
            'Debit' => $d->debit,
            'Kredit' => $d->kredit,
            'Saldo' => $saldo,
        ];
    }

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\ArrayExport($data),
        'buku-besar.xlsx'
    );
}
}