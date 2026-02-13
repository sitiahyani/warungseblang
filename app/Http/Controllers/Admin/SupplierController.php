<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // Menampilkan semua data supplier
  public function index()
{
    $supplier = Supplier::orderBy('id_supplier','desc')->get();
    return view('admin.supplier.index', compact('supplier'));
}

public function store(Request $request)
{
    $request->validate([
        'nama_supplier' => 'required',
        'no_hp' => 'required',
        'alamat' => 'required'
    ]);

    Supplier::create($request->all());

    return redirect()->route('supplier.index')
                     ->with('success','Supplier berhasil ditambahkan');
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama_supplier' => 'required',
        'no_hp' => 'required',
        'alamat' => 'required'
    ]);

    $supplier = Supplier::findOrFail($id);

    $supplier->update([
        'nama_supplier' => $request->nama_supplier,
        'no_hp' => $request->no_hp,
        'alamat' => $request->alamat
    ]);

    return redirect()->route('supplier.index')
                     ->with('success','Supplier berhasil diupdate');
}

public function destroy($id)
{
    Supplier::findOrFail($id)->delete();

    return redirect()->route('supplier.index')
                     ->with('success','Supplier berhasil dihapus');
}
}