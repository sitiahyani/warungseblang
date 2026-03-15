<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Karyawan;

class ShiftController extends Controller
{
    public function index()
    {
        $shift = Shift::with('karyawan')
                    ->orderBy('id_shift','desc')
                    ->get();

        $karyawan = Karyawan::all();

        return view('admin.shift.index',compact('shift','karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:shift,kode',
            'nama_shift' => 'required|unique:shift,nama_shift',
            'id_karyawan' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'keterangan'=>'nullable'
        ],[
            'kode.unique' => 'Kode shift sudah terdaftar',
            'nama_shift.unique' => 'Nama shift sudah terdaftar'
        ]);

        Shift::create([
            'kode' => $request->kode,
            'nama_shift' => $request->nama_shift,
            'id_karyawan' => $request->id_karyawan,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'jumlah' => 0,
            'status' => 'buka',
            'keterangan' => $request->keterangan
        ]);

        return redirect()->back()->with('success','Shift berhasil ditambahkan');
    }


    public function update(Request $request,$id)
    {
        $shift = Shift::findOrFail($id);

        $request->validate([
            'kode' => 'required|unique:shift,kode,'.$id.',id_shift',
            'nama_shift' => 'required|unique:shift,nama_shift,'.$id.',id_shift',
            'id_karyawan' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'keterangan'=>'nullable'
        ],[
            'kode.unique' => 'Kode shift sudah terdaftar',
            'nama_shift.unique' => 'Nama shift sudah terdaftar'
        ]);

        $shift->update([
            'kode' => $request->kode,
            'nama_shift' => $request->nama_shift,
            'id_karyawan' => $request->id_karyawan,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->back()->with('success','Shift berhasil diperbarui');
    }


    public function destroy($id)
    {
        Shift::findOrFail($id)->delete();

        return redirect()->back()->with('success','Shift berhasil dihapus');
    }


    public function toggleStatus($id)
    {
        $shift = Shift::findOrFail($id);

        $shift->status =
            $shift->status == 'buka'
            ? 'tutup'
            : 'buka';

        $shift->save();

        return redirect()->back()->with('success','Status shift diubah');
    }
}