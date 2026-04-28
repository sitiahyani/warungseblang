<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BahanBaku;
use App\Models\StokOpname;
use Carbon\Carbon;

class StokOpnameController extends Controller
{
    public function index()
    {
        $bahan = BahanBaku::all();

        $stokOpname = StokOpname::with('bahan')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_opname', 'desc')
            ->get();

        return view('admin.stok-opname.index', compact('bahan', 'stokOpname'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'id_bahan' => 'required|exists:bahan_baku,id_bahan',
            'stok_fisik' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        $bahan = BahanBaku::findOrFail($request->id_bahan);

        $stokSistem = $bahan->stok;
        $stokFisik = $request->stok_fisik;
        $selisih = $stokFisik - $stokSistem;

        StokOpname::create([
            'id_bahan' => $request->id_bahan,
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'stok_sistem' => $stokSistem,
            'stok_fisik' => $stokFisik,
            'selisih' => $selisih,
            'keterangan' => $request->keterangan
        ]);

        return redirect()
            ->route('stok-opname.index')
            ->with('success', 'Stok opname berhasil disimpan.');
    }
    public function sesuaikan()
{
    $data = StokOpname::where('status', 'pending')->get();

    foreach ($data as $item) {
        $bahan = BahanBaku::find($item->id_bahan);

        if ($bahan) {
            $bahan->update([
                'stok' => $item->stok_fisik
            ]);
        }

        $item->update([
            'status' => 'selesai'
        ]);
    }

    return redirect()
        ->route('stok-opname.index')
        ->with('success', 'Stok berhasil disesuaikan.');
}
}