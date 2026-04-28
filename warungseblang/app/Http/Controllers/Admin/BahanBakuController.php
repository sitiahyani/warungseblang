<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    // ===============================
    // TAMPIL DATA
    // ===============================
    public function index()
    {
        $bahan = BahanBaku::orderBy('id_bahan', 'desc')->get();
        return view('admin.bahan.index', compact('bahan'));
    }

    // ===============================
    // SIMPAN DATA
    // ===============================
    public function store(Request $request)
    {
       $request->validate([
    'nama_bahan' => 'required',
    'satuan' => 'required',
    'harga_per_satuan' => 'required|numeric',
    'stok' => 'required|numeric'
]);


        BahanBaku::create([
           'nama_bahan' => $request->nama_bahan,
        'satuan' => $request->satuan,
        'harga_per_satuan' => $request->harga_per_satuan,
        'stok' => $request->stok
        ]);

        return redirect()->route('bahan.index')
                         ->with('success', 'Bahan berhasil ditambahkan');
    }

    // ===============================
    // UPDATE DATA
    // ===============================
      public function update(Request $request, $id)
{
    $bahan = BahanBaku::findOrFail($id);

    $request->validate([
        'nama_bahan' => 'required',
        'satuan' => 'required',
        'harga_per_satuan' => 'required|numeric',
        'stok' => 'required|numeric'
    ]);

    $bahan->update([
        'nama_bahan' => $request->nama_bahan,
        'satuan' => $request->satuan,
        'harga_per_satuan' => $request->harga_per_satuan,
        'stok' => $request->stok
    ]);

    return redirect()->back()->with('success','Bahan berhasil diupdate');
}


    // ===============================
    // HAPUS DATA
    // ===============================
    public function destroy($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        $bahan->delete();

        return redirect()->route('bahan.index')
                         ->with('success', 'Bahan berhasil dihapus');
    }
}