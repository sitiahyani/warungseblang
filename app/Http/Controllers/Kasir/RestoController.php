<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Pajak;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\Diskon;
use App\Models\Tipe;
use App\Models\KodeAkun;

class RestoController extends Controller
{
    public function index()
    {
        $barang = Barang::with('kategori')
            ->orderBy('nama_barang')
            ->get();

        $tipe = \App\Models\Kategori::orderBy('nama_kategori')->get();

        $pajak = Pajak::where('status','aktif')->first();

        $diskon = Diskon::withCount('penjualan')
        ->where('status','aktif')
        ->get()
        ->filter(function($d){

            // ================= PESANAN =================
            if ($d->masa_aktif_tipe == 'pesanan') {
                return $d->penjualan_count < $d->masa_aktif_nilai;
            }

            // ================= TANGGAL =================
            if ($d->tanggal_selesai) {
                return now()->lte(\Carbon\Carbon::parse($d->tanggal_selesai)->endOfDay());
            }

            return true;
        });

        $pesananPelayan = Penjualan::with('details.barang')
            ->where('status', 'belum')
            ->where('sumber_transaksi', 'pelayan')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('kasir.penjualan.resto', compact('barang','tipe','pajak','diskon'));
    }

   public function simpan(Request $request)
{
    DB::beginTransaction();

    try {
        $items = $request->input('items', []);

        if (empty($items)) {
            throw new \Exception('Keranjang kosong');
        }

        $metode = $request->input('metode_bayar');
        $bayar = (int) $request->input('bayar', 0);
        $mode = $request->input('mode');

        $idPelanggan = $request->input('id_pelanggan');
        $namaPelanggan = $request->input('nama_pelanggan');
        $catatan = $request->input('catatan');

        // ================= HITUNG SUBTOTAL =================
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ((int)$item['harga']) * ((int)$item['qty']);
        }

        // ================= PAJAK =================
        $pajak = Pajak::where('status', 'aktif')->first();
        $nilaiPajak = 0;

        if ($pajak) {
            $nilaiPajak = $pajak->tipe_pajak === 'persen'
                ? round(($subtotal * $pajak->nilai_pajak) / 100)
                : $pajak->nilai_pajak;
        }

        // ================= DISKON =================
        $diskonId = $request->input('diskon_id');
        $diskon = null;
        $nilaiDiskon = 0;

        if ($diskonId) {
            $diskon = Diskon::lockForUpdate()->find($diskonId);

            if (!$diskon || $diskon->status !== 'aktif') {
                throw new \Exception('Diskon tidak valid');
            }

            $nilaiDiskon = $diskon->tipe_diskon === 'persen'
                ? round(($subtotal * $diskon->nilai_diskon) / 100)
                : $diskon->nilai_diskon;
        }

        // ================= TOTAL =================
        $total = max($subtotal + $nilaiPajak - $nilaiDiskon, 0);

        if ($metode === 'tunai' && $bayar < $total) {
            throw new \Exception('Uang tidak cukup');
        }

        $status = $bayar >= $total ? 'lunas' : 'belum';
        $sisaHutang = max($total - $bayar, 0);

        $sumber = session()->has('id_pelayan')
            ? 'pelayan'
            : 'kasir';

        // ================= HEADER PENJUALAN =================
        $penjualan = Penjualan::create([
            'kode_transaksi'   => Penjualan::generateKodeTransaksi(),
            'id_user'          => Auth::id() ?? 1,
            'tanggal'          => now(),
            'id_pelanggan'     => $idPelanggan,
            'nama_pelanggan'   => $namaPelanggan ?: 'Umum',
            'keterangan'       => $catatan,
            'subtotal'         => $subtotal,
            'pajak'            => $nilaiPajak,
            'diskon'           => $nilaiDiskon,
            'total'            => $total,
            'bayar'            => $bayar,
            'sisa_hutang'      => $sisaHutang,
            'metode_bayar'     => $metode,
            'status'           => $status,
            'sumber_transaksi' => $sumber,
            'id_pajak'         => $pajak->id_pajak ?? null,
            'id_diskon'        => $diskonId
        ]);

        $totalHpp = 0;

        // ================= DETAIL + STOK =================
        foreach ($items as $item) {
            $barang = Barang::lockForUpdate()->find($item['id']);

            if (!$barang) {
                throw new \Exception('Barang tidak ditemukan');
            }

            if ($barang->stok < $item['qty']) {
                throw new \Exception("Stok {$barang->nama_barang} tidak cukup");
            }

            DetailPenjualan::create([
                'id_penjualan' => $penjualan->id_penjualan,
                'id_barang'    => $barang->id_barang,
                'qty'          => $item['qty'],
                'harga'        => $item['harga'],
                'subtotal'     => $item['harga'] * $item['qty']
            ]);

            // ================= KURANGI STOK MENU =================
            $barang->decrement('stok', $item['qty']);

            // ================= KURANGI STOK BAHAN + HITUNG HPP =================
            $reseps = \App\Models\Resep::where('id_barang', $barang->id_barang)->get();

            foreach ($reseps as $resep) {
                $bahan = \App\Models\BahanBaku::lockForUpdate()
                    ->find($resep->id_bahan);

                if ($bahan) {
                    $qtyPakai = $resep->qty * $item['qty'];

                    if ($bahan->stok < $qtyPakai) {
                        throw new \Exception(
                            "Stok bahan {$bahan->nama_bahan} tidak cukup"
                        );
                    }

                    $nilaiHpp = $bahan->harga_per_satuan * $qtyPakai;
                    $totalHpp += $nilaiHpp;

                    $bahan->decrement('stok', $qtyPakai);
                }
            }
        }

        // ================= JURNAL PENJUALAN =================
        $jurnal = JurnalUmum::create([
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Penjualan resto ' . $penjualan->kode_transaksi,
            'sumber' => 'penjualan',
            'ref_id' => $penjualan->id_penjualan
        ]);

        $akunKas = KodeAkun::where('kode_akun', '1101')->first();
        $akunPenjualan = KodeAkun::where('kode_akun', '4102')->first();
        $akunDiskon = KodeAkun::where('kode_akun', '5101')->first();

        DetailJurnal::create([
            'id_jurnal' => $jurnal->id_jurnal,
            'id_akun'   => $akunKas->id_akun,
            'debit'     => $total,
            'kredit'    => 0
        ]);

        if ($nilaiDiskon > 0 && $akunDiskon) {
            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun'   => $akunDiskon->id_akun,
                'debit'     => $nilaiDiskon,
                'kredit'    => 0
            ]);
        }

        DetailJurnal::create([
            'id_jurnal' => $jurnal->id_jurnal,
            'id_akun'   => $akunPenjualan->id_akun,
            'debit'     => 0,
            'kredit'    => $subtotal
        ]);

        // ================= JURNAL HPP =================
        $akunHpp = KodeAkun::where('kode_akun', '5101')->first();
        $akunPersediaan = KodeAkun::where('kode_akun', '1101')->first();

        if ($totalHpp > 0 && $akunHpp && $akunPersediaan) {
            $jurnalHpp = JurnalUmum::create([
                'tanggal' => now()->toDateString(),
                'keterangan' => 'HPP penjualan resto ' . $penjualan->kode_transaksi,
                'sumber' => 'hpp',
                'ref_id' => $penjualan->id_penjualan
            ]);

            // debit HPP
            DetailJurnal::create([
                'id_jurnal' => $jurnalHpp->id_jurnal,
                'id_akun'   => $akunHpp->id_akun,
                'debit'     => $totalHpp,
                'kredit'    => 0
            ]);

            // kredit persediaan bahan
            DetailJurnal::create([
                'id_jurnal' => $jurnalHpp->id_jurnal,
                'id_akun'   => $akunPersediaan->id_akun,
                'debit'     => 0,
                'kredit'    => $totalHpp
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'kode'   => $penjualan->kode_transaksi,
            'total'  => $total
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function cariPelanggan(Request $request)
    {
        $keyword = $request->q;

        $pelanggan = Pelanggan::where('nama_pelanggan', 'like', "%{$keyword}%")
            ->orWhere('no_hp', 'like', "%{$keyword}%")
            ->limit(10)
            ->get([
                'id_pelanggan',
                'nama_pelanggan',
                'no_hp'
            ]);

        return response()->json($pelanggan);
    }
    
}