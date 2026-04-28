<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;
use App\Models\Pajak;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;

class RestoController extends Controller
{

    public function index()
    {

        $barang = Barang::with('kategori')
            ->orderBy('nama_barang')
            ->get();

        $pajak = Pajak::where('status','aktif')->first();

        return view('kasir.penjualan.resto',compact('barang','pajak'));

    }



  public function simpan(Request $request)
{

    DB::beginTransaction();

    try{

        // ambil data items dari JSON
        $items = $request->input('items');

        if(!$items || count($items) == 0){
            return response()->json([
                'status'=>'error',
                'message'=>'Keranjang kosong'
            ]);
        }

        /*
        =========================
        HITUNG SUBTOTAL
        =========================
        */

        $subtotal = 0;

        foreach($items as $item){

            $harga = (int)$item['harga'];
            $qty   = (int)$item['qty'];

            $subtotal += $harga * $qty;

        }

        /*
        =========================
        HITUNG PAJAK
        =========================
        */

        $pajak = Pajak::where('status','aktif')->first();

        $nilaiPajak = 0;

        if($pajak){

            if($pajak->tipe_pajak == 'persen'){
                $nilaiPajak = ($subtotal * $pajak->nilai_pajak) / 100;
            }else{
                $nilaiPajak = $pajak->nilai_pajak;
            }

        }

        $total = $subtotal + $nilaiPajak;


        /*
        =========================
        SIMPAN PENJUALAN
        =========================
        */

        $penjualan = Penjualan::create([

            'kode_transaksi' => Penjualan::generateKodeTransaksi(),

            'id_user' => Auth::id() ?? 1,

            'tanggal' => now(),

            'total' => $total,

            'bayar' => $request->bayar ?? $total,

            'sisa_bayar' => $total - ($request->bayar ?? $total),

            'metode_bayar' => $request->metode_bayar ?? 'tunai',

            'status' => ($total - ($request->bayar ?? $total)) <= 0 ? 'lunas' : 'belum'

        ]);



        /*
        =========================
        DETAIL PENJUALAN
        =========================
        */

        foreach($items as $item){

            $harga = (int)$item['harga'];
            $qty   = (int)$item['qty'];

            DetailPenjualan::create([

                'id_penjualan' => $penjualan->id_penjualan,

                'id_barang' => $item['id'],

                'qty' => $qty,

                'harga' => $harga,

                'subtotal' => $harga * $qty

            ]);

        }



        /*
        =========================
        JURNAL UMUM
        =========================
        */

        $jurnal = JurnalUmum::create([

            'tanggal'=>now(),

            'keterangan'=>'Penjualan Resto '.$penjualan->kode_transaksi,

            'sumber'=>'penjualan',

            'ref_id'=>$penjualan->id_penjualan

        ]);



        /*
        =========================
        DETAIL JURNAL
        =========================
        */

        // Debit kas / piutang
        DetailJurnal::create([

            'id_jurnal'=>$jurnal->id_jurnal,
            'id_akun'=> $request->metode_bayar == 'tunai' ? 1 : 2,
            'debit'=>$total,
            'kredit'=>0

        ]);


        // Kredit pendapatan
        DetailJurnal::create([

            'id_jurnal'=>$jurnal->id_jurnal,
            'id_akun'=>6,
            'debit'=>0,
            'kredit'=>$subtotal

        ]);



        DB::commit();

        return response()->json([

            'status'=>'success',
            'kode'=>$penjualan->kode_transaksi,
            'total'=>$total

        ]);

    }
    catch(\Exception $e){

        DB::rollback();

        return response()->json([

            'status'=>'error',

            'message'=>$e->getMessage()

        ]);

    }

}
}