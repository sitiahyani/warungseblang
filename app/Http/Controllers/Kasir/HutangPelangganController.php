<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\HistoriPembayaranHutang;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\KodeAkun;
use Illuminate\Support\Facades\DB;


class HutangPelangganController extends Controller
{
    // ================= LIST HUTANG =================
    public function index(Request $request)
    {
        $query = Penjualan::with([
            'pelangganRel',
            'historiPembayaran'
        ]);

        // 🔍 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%$search%")
                ->orWhereHas('pelangganRel', function ($q2) use ($search) {
                    $q2->where('nama_pelanggan', 'like', "%$search%");
                });
            });
        }

        $hutangPelanggan = Penjualan::with([
            'pelangganRel',
            'historiPembayaran'
        ])
        ->where('metode_bayar', '!=', 'tunai') // hanya kredit / DP
        ->orderBy('tanggal', 'desc')
        ->get();

        foreach ($hutangPelanggan as $trx) {
            $trx->sisa_fix = max($trx->total - ($trx->bayar ?? 0), 0);
            $trx->status_fix = $trx->sisa_fix <= 0 ? 'lunas' : 'belum';
        }

        return view('kasir.hutang_pelanggan.index', compact('hutangPelanggan'));
    }

    // ================= BAYAR CICILAN =================
    
public function bayar(Request $request)
{
    $request->validate([
        'id_penjualan' => 'required',
        'jumlah_bayar' => 'required|numeric|min:1',
    ]);

    DB::beginTransaction();

    try {

        $trx = Penjualan::findOrFail($request->id_penjualan);

        $total = $trx->total;
        $sudahBayar = $trx->bayar ?? 0;
        $sisa = $total - $sudahBayar;

        // ================= VALIDASI =================
        if ($sisa <= 0) {
            return back()->with('error', 'Transaksi sudah lunas');
        }

        if ($request->jumlah_bayar > $sisa) {
            return back()->with('error', 'Pembayaran melebihi sisa hutang');
        }

        $bayar = $request->jumlah_bayar;

        // ================= HITUNG =================
        $bayarBaru = $sudahBayar + $bayar;
        $sisaBaru = max($total - $bayarBaru, 0);

        // ================= HISTORI =================
        HistoriPembayaranHutang::create([
            'id_penjualan' => $trx->id_penjualan,
            'tanggal_bayar'=> now(),
            'jumlah_bayar' => $bayar,
            'sisa_hutang'  => $sisaBaru,
        ]);

        // ================= UPDATE =================
        $trx->update([
            'bayar' => $bayarBaru,
            'sisa_hutang' => $sisaBaru,
            'status' => $sisaBaru <= 0 ? 'lunas' : 'dp',
        ]);

        // ================= JURNAL =================

        $akunKas     = KodeAkun::where('kode_akun','1101')->first();
        $akunPiutang = KodeAkun::where('kode_akun','1102')->first();

        if (!$akunKas || !$akunPiutang) {
            throw new \Exception('Akun tidak ditemukan');
        }

        $jurnal = JurnalUmum::create([
            'tanggal'   => now(),
            'keterangan'=> 'Pembayaran Piutang ' . $trx->kode_transaksi,
            'sumber'    => 'pelunasan_piutang',
            'ref_id'    => $trx->id_penjualan
        ]);

        // kas masuk
        DetailJurnal::create([
            'id_jurnal' => $jurnal->id_jurnal,
            'id_akun'   => $akunKas->id_akun,
            'debit'     => $bayar,
            'kredit'    => 0
        ]);

        // piutang berkurang
        DetailJurnal::create([
            'id_jurnal' => $jurnal->id_jurnal,
            'id_akun'   => $akunPiutang->id_akun,
            'debit'     => 0,
            'kredit'    => $bayar
        ]);

        DB::commit();

        return back()->with('success', 'Pembayaran hutang berhasil');

    } catch (\Exception $e) {

        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
}