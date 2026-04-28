<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Diskon;
use App\Models\Pajak;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\KodeAkun;
use App\Models\HistoriPembayaranHutang;
use Illuminate\Support\Facades\Auth;    

class PenjualanController extends Controller
{
    public function resto()
    {
        return view('kasir.penjualan.resto');
    }

    public function homestay()
    {
        return view('kasir.penjualan.homestay');
    }

    public function wedding()
    {
        return view('kasir.penjualan.wedding');
    }

    // ================= SIMPAN TRANSAKSI =================
    public function simpan(Request $request)
    {
        DB::beginTransaction();

        try {
            if (!$request->items || count($request->items) == 0) {
                throw new \Exception("Keranjang kosong");
            }

            $metode = $request->metode_bayar;
            $bayar = $request->bayar ?? 0;

            // ================= CUSTOMER =================
            $idPelanggan = null;
            $namaPelanggan = null;

            if ($metode === 'tunai') {
                $namaPelanggan = $request->nama_pelanggan;
            } else {
                $idPelanggan = $request->id_pelanggan;
            }

            // ================= SUMBER TRANSAKSI =================
            $sumber = session()->has('id_pelayan') ? 'pelayan' : 'kasir';

            $subtotal = 0;

            // ================= BUAT HEADER PENJUALAN =================
            $penjualan = Penjualan::create([
                'kode_transaksi'   => 'INV-' . now()->format('YmdHis'),
                'tanggal'          => now(),
                'id_user'          => Auth::id(),
                'id_karyawan'      => session('id_karyawan'),
                'id_pelanggan'     => $idPelanggan,
                'nama_pelanggan'   => $namaPelanggan,
                'id_diskon'        => $request->diskon_id,
                'metode_bayar'     => $metode,
                'bayar'            => $bayar,
                'status'           => 'belum',
                'sisa_hutang'      => 0,
                'keterangan'       => $request->catatan,
                'sumber_transaksi' => $sumber,
                'subtotal'         => 0,
                'pajak'            => 0,
                'diskon'           => 0,
                'total'            => 0
            ]);

            // ================= DETAIL + STOK =================
            foreach ($request->items as $item) {
                $barang = Barang::where('id_barang', $item['id'])
                    ->lockForUpdate()
                    ->first();

                if (!$barang) {
                    throw new \Exception("Barang tidak ditemukan");
                }

                if ($barang->stok < $item['qty']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak cukup");
                }

                $sub = $item['qty'] * $item['harga'];
                $subtotal += $sub;

                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang'    => $barang->id_barang,
                    'qty'          => $item['qty'],
                    'harga'        => $item['harga'],
                    'subtotal'     => $sub
                ]);

                $barang->decrement('stok', $item['qty']);
            }

            // ================= PAJAK =================
            $pajak = Pajak::where('status', 'aktif')->first();
            $taxTotal = 0;

            if ($pajak) {
                if ($pajak->tipe_pajak == 'persen') {
                    $taxTotal = round(($subtotal * $pajak->nilai_pajak) / 100);
                } else {
                    $taxTotal = $pajak->nilai_pajak;
                }
            }

            // ================= DISKON =================
            $diskonTotal = 0;

            if ($request->diskon_id) {
                $diskon = Diskon::lockForUpdate()->find($request->diskon_id);

                if ($diskon) {
                    // cek diskon pesanan masih tersedia
                    if (
                        $diskon->masa_aktif_tipe === 'pesanan' &&
                        $diskon->penjualan_count >= $diskon->masa_aktif_nilai
                    ) {
                        throw new \Exception("Diskon sudah habis");
                    }

                    if ($diskon->tipe_diskon == 'persen') {
                        $diskonTotal = round(($subtotal * $diskon->nilai_diskon) / 100);
                    } else {
                        $diskonTotal = $diskon->nilai_diskon;
                    }

                    if ($diskon->masa_aktif_tipe === 'pesanan') {
                        $diskon->increment('penjualan_count');
                    }
                }
            }

            // ================= TOTAL =================
            $total = max($subtotal + $taxTotal - $diskonTotal, 0);

            // ================= VALIDASI PEMBAYARAN =================
            if ($metode == 'tunai' && $bayar < $total) {
                throw new \Exception("Uang kurang");
            }

            if ($metode == 'kredit' && $bayar < 50000) {
                throw new \Exception("DP minimal Rp 50.000");
            }

            // ================= STATUS =================
            $sisaHutang = 0;
            $status = 'lunas';

            if ($metode == 'kredit') {
                $sisaHutang = max($total - $bayar, 0);
                $status = $sisaHutang > 0 ? 'dp' : 'lunas';
            }

            // ================= UPDATE HEADER =================
            $penjualan->update([
                'subtotal'    => $subtotal,
                'pajak'       => $taxTotal,
                'diskon'      => $diskonTotal,
                'total'       => $total,
                'bayar'       => $bayar,
                'sisa_hutang' => $sisaHutang,
                'status'      => $status
            ]);
            // ================= BUAT JURNAL =================
            $jurnal = JurnalUmum::create([
                'tanggal'   => now(),
                'keterangan'=> 'Penjualan ' . $penjualan->kode_transaksi,
                'sumber'    => 'penjualan',
                'ref_id'    => $penjualan->id_penjualan
            ]);

            // ================= AMBIL AKUN =================
            $akunKas        = KodeAkun::where('kode_akun','1101')->first();
            $akunPiutang    = KodeAkun::where('kode_akun','1102')->first();
            $akunPendapatan = KodeAkun::where('kode_akun','4101')->first();
            $akunDiskon     = KodeAkun::where('kode_akun','4103')->first();
            $akunPajak      = KodeAkun::where('kode_akun','2102')->first();


            // ================= DEBIT =================

            // 🔹 TUNAI
            if ($metode == 'tunai') {

                DetailJurnal::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_akun'   => $akunKas->id_akun,
                    'debit'     => $total, // full masuk kas
                    'kredit'    => 0
                ]);
            }

            // 🔹 KREDIT (DP + PIUTANG)
            if ($metode == 'kredit') {

                // DP masuk kas
                if ($bayar > 0) {
                    DetailJurnal::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'id_akun'   => $akunKas->id_akun,
                        'debit'     => $bayar,
                        'kredit'    => 0
                    ]);
                }

                // sisa jadi piutang
                if ($sisaHutang > 0) {
                    DetailJurnal::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'id_akun'   => $akunPiutang->id_akun,
                        'debit'     => $sisaHutang,
                        'kredit'    => 0
                    ]);
                }
            }

            // 🔹 DISKON (pengurang pendapatan)
            if ($diskonTotal > 0) {
                DetailJurnal::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_akun'   => $akunDiskon->id_akun,
                    'debit'     => $diskonTotal,
                    'kredit'    => 0
                ]);
            }


            // ================= KREDIT =================

            // 🔹 Pendapatan (SELALU dari subtotal)
            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun'   => $akunPendapatan->id_akun,
                'debit'     => 0,
                'kredit'    => $subtotal
            ]);

            // 🔹 Pajak (utang pajak)
            if ($taxTotal > 0) {
                DetailJurnal::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_akun'   => $akunPajak->id_akun,
                    'debit'     => 0,
                    'kredit'    => $taxTotal
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'kode' => $penjualan->kode_transaksi,
                'total' => $total,
                'sisa_hutang' => $sisaHutang,
                'status_transaksi' => $status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}