<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return view('admin.kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:kategori,nama_kategori'
        ],[
            'nama_kategori.unique' => 'Data sudah terdaftar'
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return back()->with('success','Kategori berhasil ditambahkan');
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:kategori,nama_kategori,'.$id.',id_kategori'
        ],[
            'nama_kategori.unique' => 'Data sudah terdaftar'
        ]);

        $kategori = Kategori::findOrFail($id);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return back()->with('success','Kategori berhasil diupdate');
    }

    public function destroy($id)
{
    $kategori = Kategori::findOrFail($id);

    $dipakaiTipe = DB::table('tipe')->where('id_kategori', $id)->exists();
    $dipakaiLayanan = DB::table('layanan')->where('id_kategori', $id)->exists();
    $dipakaiPenjualan = DB::table('penjualan')->where('id_kategori', $id)->exists();

    if ($dipakaiTipe || $dipakaiLayanan || $dipakaiPenjualan) {
        return back()->with('error', 'Kategori tidak bisa dihapus karena masih digunakan');
    }

    $kategori->delete();

    return back()->with('success', 'Kategori berhasil dihapus');
}
}