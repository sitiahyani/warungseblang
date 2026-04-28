<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KodeAkun;
use Illuminate\Http\Request;

class KodeAkunController extends Controller
{
    public function index()
    {
        $akun = KodeAkun::orderBy('kode_akun','asc')->get();
        return view('admin.kode_akun.index', compact('akun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_akun' => 'required|unique:akun,kode_akun',
            'nama_akun' => 'required',
            'jenis_akun' => 'required'
        ]);

        KodeAkun::create($request->only([
            'kode_akun',
            'nama_akun',
            'jenis_akun'
        ]));

        return redirect()->route('kode-akun.index')
                         ->with('success','Kode akun berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $akun = KodeAkun::findOrFail($id);

        $request->validate([
            'kode_akun' => 'required|unique:akun,kode_akun,'.$id.',id_akun',
            'nama_akun' => 'required',
            'jenis_akun' => 'required'
        ]);

        $akun->update($request->only([
            'kode_akun',
            'nama_akun',
            'jenis_akun'
        ]));

        return redirect()->route('kode-akun.index')
                         ->with('success','Kode akun berhasil diupdate');
    }

    public function destroy($id)
    {
        $akun = KodeAkun::findOrFail($id);
        $akun->delete();

        return redirect()->route('kode-akun.index')
                         ->with('success','Kode akun berhasil dihapus');
    }
}