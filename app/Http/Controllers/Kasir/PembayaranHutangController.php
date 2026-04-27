<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hutang;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\PembayaranHutang;
use App\Models\KodeAkun;
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

            $bayar = $request->jumlah_bayar;

            // ================= SIMPAN PEMBAYARAN =================
            PembayaranHutang::create([
                'id_hutang'    => $hutang->id_hutang,
                'tanggal'      => now(),
                'jumlah_bayar' => $bayar
            ]);

            // ================= UPDATE HUTANG =================
            $hutang->sisa -= $bayar;

            if ($hutang->sisa <= 0) {
                $hutang->status = 'lunas';
                $hutang->sisa = 0;
            }

            $hutang->save();

            // ================= JURNAL =================

            // ambil akun dari kode
            $akunHutang = KodeAkun::where('kode_akun','2101')->first(); // Utang Usaha
            $akunKas    = KodeAkun::where('kode_akun','1101')->first(); // Kas

            if (!$akunHutang || !$akunKas) {
                throw new \Exception('Akun tidak ditemukan (cek kode_akun)');
            }

            $jurnal = JurnalUmum::create([
                'tanggal'   => now(),
                'keterangan'=> 'Pembayaran Hutang',
                'sumber'    => 'bayar_hutang',
                'ref_id'    => $hutang->id_hutang
            ]);

            // 🔹 Hutang berkurang (DEBIT)
            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun'   => $akunHutang->id_akun,
                'debit'     => $bayar,
                'kredit'    => 0
            ]);

            // 🔹 Kas keluar (KREDIT)
            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun'   => $akunKas->id_akun,
                'debit'     => 0,
                'kredit'    => $bayar
            ]);

            DB::commit();

            return back()->with('success','Hutang berhasil dibayar');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }
}