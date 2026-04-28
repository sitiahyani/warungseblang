<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Tipe;
use App\Models\Kategori;
use Illuminate\Http\Request;

class LayananController extends Controller
{

    public function index()
    {
        $layanan  = Layanan::with(['tipe','kategori'])->get();
        $kategori = Kategori::all();
        $tipe     = Tipe::all();
        return view(
            'admin.layanan.index',
            compact('layanan','tipe','kategori')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tipe'        => 'required',
            'kode_layanan'   => 'required',
            'nama_layanan'   => 'required',
            'id_kategori'    => 'required',
            'harga'          => 'required|numeric',
            'deskripsi'      => 'nullable',
            'status'         => 'required'
        ]);
        Layanan::create([
            'id_tipe'        => $request->id_tipe,
            'kode_layanan'   => $request->kode_layanan,
            'nama_layanan'   => $request->nama_layanan,
            'id_kategori'    => $request->id_kategori,
            'harga'          => $request->harga,
            'deskripsi'      => $request->deskripsi,
            'status'         => $request->status
        ]);
        return redirect()
                ->route('layanan.index')
                ->with('success','Data layanan berhasil ditambahkan');
    }


    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);
        $request->validate([
            'id_tipe'        => 'required',
            'kode_layanan'   => 'required',
            'nama_layanan'   => 'required',
            'id_kategori'    => 'required',
            'harga'          => 'required|numeric',
            'deskripsi'      => 'nullable',
            'status'         => 'required'
        ]);
        $layanan->update([
            'id_tipe'        => $request->id_tipe,
            'kode_layanan'   => $request->kode_layanan,
            'nama_layanan'   => $request->nama_layanan,
            'id_kategori'    => $request->id_kategori,
            'harga'          => $request->harga,
            'deskripsi'      => $request->deskripsi,
            'status'         => $request->status
        ]);
        return redirect()
                ->route('layanan.index')
                ->with('success','Data layanan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->delete();
        return redirect()
                ->route('layanan.index')
                ->with('success','Data layanan berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->status =
            $layanan->status == 'aktif'
            ? 'nonaktif'
            : 'aktif';
        $layanan->save();
        return redirect()
                ->back()
                ->with('success','Status layanan berhasil diubah');
    }
}