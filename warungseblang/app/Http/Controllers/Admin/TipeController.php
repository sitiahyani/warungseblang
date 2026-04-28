<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tipe;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipeController extends Controller
{
    public function index()
    {
        $tipe = Tipe::with('kategori')->get();
        $kategori = Kategori::all();

        return view('admin.tipe.index', compact('tipe','kategori'));
    }

    public function store(Request $request)
    {
        Tipe::create([
            'id_kategori' => $request->id_kategori,
            'nama_tipe'   => $request->nama_tipe,
        ]);

        return redirect()->back()->with('success','Tipe berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $tipe = Tipe::findOrFail($id);

        $tipe->update([
            'id_kategori' => $request->id_kategori,
            'nama_tipe'   => $request->nama_tipe,
        ]);

        return redirect()->back()->with('success','Tipe berhasil diupdate');
    }

    public function destroy($id)
    {
        $tipe = Tipe::findOrFail($id);

        // 🔥 cek apakah dipakai di layanan
        $dipakai = DB::table('layanan')
            ->where('id_tipe', $id)
            ->exists();

        if ($dipakai) {
            return back()->with('error', 'Tipe tidak bisa dihapus karena masih digunakan di layanan');
        }

        $tipe->delete();

        return back()->with('success','Tipe berhasil dihapus');
    }
}