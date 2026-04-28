<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use Illuminate\Http\Request;

class DiskonController extends Controller
{
    public function index()
    {
        $diskon = Diskon::orderBy('id_diskon','desc')->get();
        return view('admin.diskon.index', compact('diskon'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_diskon' => 'required|unique:diskon,nama_diskon',
            'tipe_diskon' => 'required',
            'nilai_diskon' => 'required'
        ],[
            'nama_diskon.unique' => 'Data sudah terdaftar'
        ]);
        Diskon::create([
            'nama_diskon' => $request->nama_diskon,
            'tipe_diskon' => $request->tipe_diskon,
            'nilai_diskon' => $request->nilai_diskon,
            'masa_aktif_tipe' => $request->masa_aktif_tipe,
            'masa_aktif_nilai' => $request->masa_aktif_nilai,
            'status' => $request->status ?? 'aktif'
        ]);
        return redirect()->back()->with('success','Diskon berhasil ditambahkan');
    }

    public function update(Request $request,$id)
    {
        $diskon = Diskon::findOrFail($id);
        $request->validate([
            'nama_diskon' => 'required|unique:diskon,nama_diskon,'.$id.',id_diskon',
            'tipe_diskon' => 'required',
            'nilai_diskon' => 'required'
        ],[
            'nama_diskon.unique' => 'Data sudah terdaftar'
        ]);

        $diskon->update([
            'nama_diskon' => $request->nama_diskon,
            'tipe_diskon' => $request->tipe_diskon,
            'nilai_diskon' => $request->nilai_diskon,
            'masa_aktif_tipe' => $request->masa_aktif_tipe,
            'masa_aktif_nilai' => $request->masa_aktif_nilai,
            'status' => $request->status
        ]);
        return redirect()->back()->with('success','Diskon berhasil diupdate');
    }

    public function destroy($id)
    {
        Diskon::findOrFail($id)->delete();
        return redirect()->back()->with('success','Diskon berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $diskon = Diskon::findOrFail($id);
        $diskon->status = $diskon->status == 'aktif'
            ? 'nonaktif'
            : 'aktif';
        $diskon->save();
        return redirect()->back()->with('success','Status berhasil diubah');
    }
}