<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modal;
use App\Models\KodeAkun; // ← sesuai tabel akun kamu
use App\Services\JurnalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModalController extends Controller
{
    public function index()
    {
        $modal = Modal::orderBy('tanggal','desc')->get();

        $totalSetor = Modal::where('jenis','tambah')->sum('jumlah');
        $totalTarik = Modal::where('jenis','tarik')->sum('jumlah');
        $saldo = $totalSetor - $totalTarik;

        return view('admin.modal.index', compact(
            'modal',
            'totalSetor',
            'totalTarik',
            'saldo'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah'  => 'required|numeric|min:1',
            'jenis'   => 'required|in:tambah,tarik',
            'keterangan' => 'nullable'
        ]);

        DB::transaction(function() use ($request) {

            $modal = Modal::create([
                'tanggal'    => $request->tanggal,
                'jumlah'     => $request->jumlah,
                'jenis'      => $request->jenis,
                'keterangan' => $request->keterangan
            ]);

            // ===== AMBIL AKUN SESUAI DATABASE KAMU =====

            $kasAkun   = KodeAkun::where('kode_akun','1101')->first();
            $modalAkun = KodeAkun::where('kode_akun','3101')->first();
            $priveAkun = KodeAkun::where('kode_akun','3102')->first();

            if (!$kasAkun || !$modalAkun || !$priveAkun) {
                abort(500,'Master akun belum lengkap');
            }

            $kas   = $kasAkun->id_akun;
            $modalPemilik = $modalAkun->id_akun;
            $prive = $priveAkun->id_akun;

            // ===== JURNAL =====

            if ($request->jenis == 'tambah') {

                JurnalService::simpan(
                    $request->tanggal,
                    'Setoran Modal',
                    'modal',
                    $modal->id_modal,
                    [
                        ['id_akun' => $kas, 'debit' => $request->jumlah],
                        ['id_akun' => $modalPemilik, 'kredit' => $request->jumlah],
                    ]
                );

            } else {

                JurnalService::simpan(
                    $request->tanggal,
                    'Penarikan Modal',
                    'modal',
                    $modal->id_modal,
                    [
                        ['id_akun' => $prive, 'debit' => $request->jumlah],
                        ['id_akun' => $kas, 'kredit' => $request->jumlah],
                    ]
                );
            }

        });

        return back()->with('success','Modal & jurnal berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $modal = Modal::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'jumlah'  => 'required|numeric|min:1',
            'jenis'   => 'required|in:tambah,tarik',
            'keterangan' => 'nullable'
        ]);

        $modal->update($request->all());

        return back()->with('success','Data modal berhasil diupdate');
    }
}