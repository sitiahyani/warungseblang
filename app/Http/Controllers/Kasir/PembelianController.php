<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\BahanBaku;
use App\Models\DetailJurnal;
use App\Models\JurnalUmum;
use App\Models\Hutang;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index()
    {
        $supplier = Supplier::orderBy('nama_supplier')->get();
        $bahan    = BahanBaku::orderBy('nama_bahan')->get();

        return view('kasir.pembelian.index', compact('supplier','bahan'));
    }

    public function store(Request $request)
{
    $request->validate([
        'id_supplier'  => 'required|exists:supplier,id_supplier',
        'id_bahan'     => 'required|exists:bahan_baku,id_bahan',
        'qty'          => 'required|numeric|min:1',
        'harga'        => 'required|numeric|min:0',
        'metode_bayar' => 'required|in:tunai,kredit'
    ]);

    DB::beginTransaction();

    try {

        $subtotal = $request->qty * $request->harga;

        // ==========================
        // 1️⃣ SIMPAN PEMBELIAN
        // ==========================
        $pembelian = Pembelian::create([
            'tanggal'      => now(),
            'id_supplier'  => $request->id_supplier,
            'total'        => $subtotal,
            'metode_bayar' => $request->metode_bayar,
            'status'       => $request->metode_bayar == 'kredit' ? 'belum' : 'lunas'
        ]);

        // ==========================
        // 2️⃣ SIMPAN DETAIL PEMBELIAN
        // ==========================
        DetailPembelian::create([
            'id_pembelian' => $pembelian->id_pembelian,
            'id_bahan'     => $request->id_bahan,
            'qty'          => $request->qty,
            'harga'        => $request->harga,
            'subtotal'     => $subtotal
        ]);

        // ==========================
        // 3️⃣ UPDATE STOK
        // ==========================
        $bahan = BahanBaku::findOrFail($request->id_bahan);
        $bahan->stok += $request->qty;
        $bahan->save();

        // ==========================
        // 4️⃣ BUAT HEADER JURNAL
        // ==========================
        $jurnal = JurnalUmum::create([
            'tanggal'    => now(),
            'keterangan' => 'Pembelian Bahan',
            'sumber'     => 'pembelian',
            'ref_id'     => $pembelian->id_pembelian
        ]);

        // ==========================
        // 5️⃣ BUAT DETAIL JURNAL
        // ==========================

        $akunBeban  = 8; // Beban Pembelian
        $akunKas    = 1; // Kas
        $akunHutang = 3; // Hutang Usaha

        if ($request->metode_bayar == 'tunai') {

            // Debit Beban
            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun'   => $akunBeban,
                'debit'     => $subtotal,
                'kredit'    => 0
            ]);

            // Kredit Kas
            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun'   => $akunKas,
                'debit'     => 0,
                'kredit'    => $subtotal
            ]);

        } else {

            // Debit Beban
            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun'   => $akunBeban,
                'debit'     => $subtotal,
                'kredit'    => 0
            ]);

            // Kredit Hutang
            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun'   => $akunHutang,
                'debit'     => 0,
                'kredit'    => $subtotal
            ]);

            // ==========================
            // 6️⃣ SIMPAN HUTANG (JIKA KREDIT)
            // ==========================
            Hutang::create([
                'id_pembelian' => $pembelian->id_pembelian,
                'id_supplier'  => $request->id_supplier,
                'total'        => $subtotal,
                'sisa'         => $subtotal,
                'status'       => 'belum'
            ]);
        }

        DB::commit();

        return redirect()
            ->route('kasir.pembelian')
            ->with('success', 'Transaksi pembelian berhasil disimpan');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
    }
}
}