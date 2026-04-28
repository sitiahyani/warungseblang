<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Tipe;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LayananController extends Controller
{

    public function index()
    {
        $layanan  = Layanan::with(['tipe','kategoriRel'])->get();
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
            'status'         => 'required',
            'gambar'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $namaGambar = null;

        if ($request->hasFile('gambar')) {

            $file = $request->file('gambar');

            // nama file rapi & aman
            $namaGambar = time().'_'.Str::slug($request->nama_layanan).'.'.$file->extension();

            // 🔥 SIMPAN LANGSUNG KE PUBLIC (PASTI MASUK)
            $file->move(public_path('storage/layanan'), $namaGambar);
        }

        Layanan::create([
            'id_tipe'        => $request->id_tipe,
            'kode_layanan'   => $request->kode_layanan,
            'nama_layanan'   => $request->nama_layanan,
            'id_kategori'    => $request->id_kategori,
            'harga'          => $request->harga,
            'deskripsi'      => $request->deskripsi,
            'status'         => $request->status,
            'gambar'         => $namaGambar
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
            'status'         => 'required',
            'gambar'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $namaGambar = $layanan->gambar;

        if ($request->hasFile('gambar')) {

            $file = $request->file('gambar');

            $namaGambar = time().'_'.Str::slug($request->nama_layanan).'.'.$file->extension();

            // 🔥 SIMPAN KE PUBLIC
            $file->move(public_path('storage/layanan'), $namaGambar);
        }

        $layanan->update([
            'id_tipe'        => $request->id_tipe,
            'kode_layanan'   => $request->kode_layanan,
            'nama_layanan'   => $request->nama_layanan,
            'id_kategori'    => $request->id_kategori,
            'harga'          => $request->harga,
            'deskripsi'      => $request->deskripsi,
            'status'         => $request->status,
            'gambar'         => $namaGambar
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