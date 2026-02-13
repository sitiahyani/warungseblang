<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\BahanBaku;
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

            // ============================
            // 1️⃣ SIMPAN PEMBELIAN
            // ============================
            $pembelian = Pembelian::create([
                'tanggal'      => now(),
                'id_supplier'  => $request->id_supplier,
                'total'        => $subtotal,
                'metode_bayar' => $request->metode_bayar,
                'status'       => $request->metode_bayar == 'kredit' ? 'belum' : 'lunas'
            ]);

            // ============================
            // 2️⃣ SIMPAN DETAIL
            // ============================
            DetailPembelian::create([
                'id_pembelian' => $pembelian->id_pembelian,
                'id_bahan'     => $request->id_bahan,
                'qty'          => $request->qty,
                'harga'        => $request->harga,
                'subtotal'     => $subtotal
            ]);

            // ============================
            // 3️⃣ UPDATE STOK
            // ============================
            $bahan = BahanBaku::findOrFail($request->id_bahan);
            $bahan->stok += $request->qty;
            $bahan->save();

            // ============================
            // 4️⃣ JURNAL AKUNTANSI
            // ============================

            if ($request->metode_bayar == 'tunai') {

                // Debit Persediaan
                JurnalUmum::create([
                    'tanggal'    => now(),
                    'keterangan' => 'Pembelian Tunai',
                    'debit'      => $subtotal,
                    'kredit'     => 0
                ]);

                // Kredit Kas
                JurnalUmum::create([
                    'tanggal'    => now(),
                    'keterangan' => 'Kas Keluar',
                    'debit'      => 0,
                    'kredit'     => $subtotal
                ]);

            } else {

                // Debit Persediaan
                JurnalUmum::create([
                    'tanggal'    => now(),
                    'keterangan' => 'Pembelian Kredit',
                    'debit'      => $subtotal,
                    'kredit'     => 0
                ]);

                // Kredit Hutang
                JurnalUmum::create([
                    'tanggal'    => now(),
                    'keterangan' => 'Hutang Supplier',
                    'debit'      => 0,
                    'kredit'     => $subtotal
                ]);

                // ============================
                // 5️⃣ SIMPAN KE TABEL HUTANG
                // ============================
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
                ->with('success','Transaksi pembelian berhasil disimpan');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error','Terjadi kesalahan: '.$e->getMessage());
        }
    }
}