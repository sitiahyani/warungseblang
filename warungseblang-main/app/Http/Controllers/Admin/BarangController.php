<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Tipe;
use App\Models\Resep;
use App\Models\HppProduksi;
use App\Models\HppDetail;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $barang = Barang::with(['kategori','tipe'])
            ->when($search, function ($query) use ($search) {
                $query->where('nama_barang', 'like', "%$search%")
                      ->orWhere('kode_barang', 'like', "%$search%");
            })
            ->orderBy('id_barang', 'desc')
            ->get();

        $kategori   = Kategori::all();
        $tipe       = Tipe::all();
        $bahan_baku = BahanBaku::all();

        return view('admin.barang.index', compact(
            'barang',
            'kategori',
            'tipe',
            'bahan_baku'
        ));
    }

    public function store(Request $request)
    {
    
    $data = $request->validate([
    'id_kategori' => 'required|exists:kategori,id_kategori',
    'id_tipe'     => 'nullable|exists:tipe,id_tipe',
    'kode_barang' => 'required',
    'nama_barang' => 'required',
    'satuan'      => 'required',
    'harga_jual'  => 'nullable|numeric',
    'gambar'      => 'nullable|image',
    'stok'        => 'nullable|numeric'
]);

$data['stok'] = $request->stok ?? 0;

        // jika tipe kosong, pastikan null
        $data['id_tipe'] = $request->id_tipe ?: null;
    
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')
                                      ->store('barang', 'public');
        }

        $data['status'] = 'aktif';

        Barang::create($data);

        return redirect()
            ->back()
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

       $data = $request->validate([
    'id_kategori' => 'required|exists:kategori,id_kategori',
    'id_tipe'     => 'nullable|exists:tipe,id_tipe',
    'kode_barang' => 'required',
    'nama_barang' => 'required',
    'satuan'      => 'required',
    'harga_jual'  => 'nullable|numeric',
    'gambar'      => 'nullable|image',
     'stok'        => 'nullable|numeric'
]);

$data['stok'] = $request->stok ?? 0;


        $data['id_tipe'] = $request->id_tipe ?: null;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')
                                      ->store('barang', 'public');
        }

        $barang->update($data);

        return redirect()
            ->back()
            ->with('success', 'Barang berhasil diupdate');
    }

    public function destroy($id)
    {
        Barang::destroy($id);

        return back()->with('success', 'Barang dihapus');
    }

    public function status($id)
    {
        $barang = Barang::findOrFail($id);

        $barang->status = $barang->status == 'aktif'
            ? 'nonaktif'
            : 'aktif';

        $barang->save();

        return back();
    }

    public function hpp($id)
    {
        $barang = Barang::with(['kategori','tipe'])
                        ->findOrFail($id);

        $bahan_baku = BahanBaku::all();

        return view('admin.barang.hpp', compact(
            'barang',
            'bahan_baku'
        ));
    }

    public function simpanHpp(Request $request, $id)
{
    $barang = Barang::findOrFail($id);

    $request->validate([
        'bahan_id' => 'required|array',
        'qty'      => 'required|array'
    ]);

    DB::beginTransaction();

    try {

        // Hapus resep lama
        Resep::where('id_barang',$id)->delete();

        $total = 0;

        foreach ($request->bahan_id as $index => $bahanId) {

            $qty = $request->qty[$index];

            $bahan = BahanBaku::find($bahanId);

            $subtotal = $bahan->harga_per_satuan * $qty;

            $total += $subtotal;

            Resep::create([
                'id_barang' => $id,
                'id_bahan'  => $bahanId,
                'qty'       => $qty
            ]);
        }

        // Simpan ke hpp_produksi
        $hpp = HppProduksi::create([
            'id_barang' => $id,
            'tanggal'   => now(),
            'total_biaya' => $total,
            'hpp_unit'    => $total,
            'status'      => 'aktif'
        ]);

        // Update barang
        $barang->hpp = $total;
        $barang->save();

        DB::commit();

        return redirect()
            ->route('barang.index')
            ->with('success','HPP berhasil dihitung & disimpan');

    } catch (\Exception $e) {

        DB::rollback();
        return back()->with('error','Terjadi kesalahan');
    }
}
public function resep($id)
{
    $barang = Barang::findOrFail($id);
    $bahan_baku = BahanBaku::all();

    return view('admin.barang.resep', compact(
        'barang',
        'bahan_baku'
    ));
}
public function simpanResep(Request $request, $id)
{
    $barang = Barang::findOrFail($id);

    DB::transaction(function () use ($request, $barang) {

        // Hapus resep lama
        Resep::where('id_barang', $barang->id_barang)->delete();

        $totalHpp = 0;

        if ($request->bahan_id) {

            foreach ($request->bahan_id as $index => $bahanId) {

                $qty = $request->qty[$index] ?? 0;

                if ($qty > 0) {

                    $bahan = \App\Models\BahanBaku::find($bahanId);

                    $subtotal = $bahan->harga_per_satuan * $qty;

                    $totalHpp += $subtotal;

                    Resep::create([
                        'id_barang' => $barang->id_barang,
                        'id_bahan'  => $bahanId,
                        'qty'       => $qty,
                    ]);
                }
            }
        }

        // Update HPP barang
        $barang->update([
            'hpp' => $totalHpp
        ]);
    });

    return redirect()
            ->route('barang.index')
            ->with('success','Resep & HPP berhasil disimpan');
}
}