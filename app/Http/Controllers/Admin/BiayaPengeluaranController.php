<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KodeAkun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiayaPengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $pengeluaran = DB::table('biaya_pengeluaran')
            ->join('akun','akun.id_akun','=','biaya_pengeluaran.id_akun')
            ->select('biaya_pengeluaran.*','akun.nama_akun','akun.kode_akun')
            ->when($search, function($q) use ($search){
                $q->where('keterangan','like',"%$search%");
            })
            ->orderBy('tanggal','desc')
            ->get();

        $akun = KodeAkun::where('jenis_akun','beban')->get();

        return view('admin.biaya.index', compact('pengeluaran','akun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_akun' => 'required|exists:akun,id_akun',
            'jumlah'  => 'required|numeric|min:1'
        ]);

        DB::transaction(function() use ($request){

            $biayaId = DB::table('biaya_pengeluaran')->insertGetId([
                'tanggal'    => $request->tanggal,
                'keterangan' => $request->keterangan,
                'id_akun'    => $request->id_akun,
                'jumlah'     => $request->jumlah,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $jurnalId = DB::table('jurnal_umum')->insertGetId([
                'tanggal'    => $request->tanggal,
                'keterangan' => $request->keterangan,
                'sumber'     => 'biaya',
                'ref_id'     => $biayaId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $kas = DB::table('akun')->where('kode_akun','1101')->first();

            DB::table('detail_jurnal')->insert([
                [
                    'id_jurnal'=>$jurnalId,
                    'id_akun'=>$request->id_akun,
                    'debit'=>$request->jumlah,
                    'kredit'=>0,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ],
                [
                    'id_jurnal'=>$jurnalId,
                    'id_akun'=>$kas->id_akun,
                    'debit'=>0,
                    'kredit'=>$request->jumlah,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ]
            ]);

        });

        return back()->with('success','Biaya berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required',
            'id_akun' => 'required',
            'jumlah'  => 'required|numeric'
        ]);

        DB::table('biaya_pengeluaran')
            ->where('id_pengeluaran',$id)
            ->update([
                'tanggal'=>$request->tanggal,
                'keterangan'=>$request->keterangan,
                'id_akun'=>$request->id_akun,
                'jumlah'=>$request->jumlah,
                'updated_at'=>now()
            ]);

        return back()->with('success','Biaya berhasil diupdate');
    }

    public function destroy($id)
    {
        DB::table('biaya_pengeluaran')
            ->where('id_pengeluaran',$id)
            ->delete();

        return back()->with('success','Biaya berhasil dihapus');
    }
}