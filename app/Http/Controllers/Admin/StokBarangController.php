<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;

class StokBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $barang = Barang::orderBy('nama_barang')->get();
    return view('admin.stok_barang.index', compact('barang'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $barang = Barang::findOrFail($id);

    $jumlah = (int) $request->jumlah;

    if ($request->aksi == 'tambah') {
        $barang->stok += $jumlah;
    }

    if ($request->aksi == 'kurang') {

        if ($barang->stok < $jumlah) {
            return back()->with('error','Stok tidak cukup!');
        }

        $barang->stok -= $jumlah;
    }

    $barang->keterangan = $request->keterangan;
    $barang->save();

    return back()->with('success','Stok berhasil diperbarui');
}


}