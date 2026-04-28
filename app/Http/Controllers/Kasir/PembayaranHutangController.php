<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hutang;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\PembayaranHutang;
use Illuminate\Support\Facades\DB;

class PembayaranHutangController extends Controller
{
    public function index()
{
    $hutangs = Hutang::with('supplier')
                ->where('status','belum')
                ->get();

    return view('kasir.hutang.index', compact('hutangs'));
}
public function bayar(Request $request)
{
    $request->validate([
        'id_hutang' => 'required',
        'jumlah_bayar' => 'required|numeric|min:1'
    ]);

    DB::beginTransaction();

    try {

        $hutang = Hutang::findOrFail($request->id_hutang);

        if ($request->jumlah_bayar > $hutang->sisa) {
            return back()->with('error','Jumlah melebihi sisa hutang');
        }

        // Simpan pembayaran
        PembayaranHutang::create([
            'id_hutang' => $hutang->id_hutang,
            'tanggal' => now(),
            'jumlah_bayar' => $request->jumlah_bayar
        ]);

        // Update sisa
        $hutang->sisa -= $request->jumlah_bayar;

        if ($hutang->sisa == 0) {
            $hutang->status = 'lunas';
        }

        $hutang->save();

        // ==========================
        // BUAT JURNAL
        // ==========================

        $akunHutang = 3; // Hutang
        $akunKas = 1;    // Kas

        $jurnal = JurnalUmum::create([
            'tanggal' => now(),
            'keterangan' => 'Pembayaran Hutang',
            'sumber' => 'bayar_hutang',
            'ref_id' => $hutang->id_hutang
        ]);

        DetailJurnal::create([
            'id_jurnal' => $jurnal->id_jurnal,
            'id_akun' => $akunHutang,
            'debit' => $request->jumlah_bayar,
            'kredit' => 0
        ]);

        DetailJurnal::create([
            'id_jurnal' => $jurnal->id_jurnal,
            'id_akun' => $akunKas,
            'debit' => 0,
            'kredit' => $request->jumlah_bayar
        ]);

        DB::commit();

        return back()->with('success','Hutang berhasil dibayar');

    } catch (\Exception $e) {

        DB::rollBack();
        return back()->with('error',$e->getMessage());
    }
}
}