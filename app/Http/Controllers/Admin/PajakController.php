<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pajak;
use Illuminate\Http\Request;

class PajakController extends Controller
{
    public function index()
    {
        $pajak = Pajak::orderBy('id_pajak','desc')->get();
        return view('admin.pajak.index',compact('pajak'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pajak' => 'required|unique:pajak,nama_pajak',
            'tipe_pajak' => 'required',
            'nilai_pajak' => 'required',
            'status' => 'required'
        ],[
            'nama_pajak.unique' => 'data sudah terdaftar'
        ]);
        /*
        Jika pajak baru diset aktif
        maka semua pajak lain dibuat nonaktif
        */
        if($request->status == 'aktif'){
            Pajak::where('status','aktif')
                ->update(['status'=>'nonaktif']);
        }
        Pajak::create([
            'nama_pajak' => $request->nama_pajak,
            'tipe_pajak' => $request->tipe_pajak,
            'nilai_pajak' => $request->nilai_pajak,
            'status' => $request->status
        ]);
        return redirect()->back()
            ->with('success','Data pajak berhasil ditambahkan');
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'nama_pajak' => 'required|unique:pajak,nama_pajak,'.$id.',id_pajak',
            'tipe_pajak' => 'required',
            'nilai_pajak' => 'required',
            'status' => 'required'
        ],[
            'nama_pajak.unique' => 'data sudah terdaftar'
        ]);
        $pajak = Pajak::findOrFail($id);

        /*
        jika diubah jadi aktif
        maka pajak lain nonaktif
        */
        if($request->status == 'aktif'){
            Pajak::where('status','aktif')
                ->where('id_pajak','!=',$id)
                ->update(['status'=>'nonaktif']);
        }
        $pajak->update([
            'nama_pajak' => $request->nama_pajak,
            'tipe_pajak' => $request->tipe_pajak,
            'nilai_pajak' => $request->nilai_pajak,
            'status' => $request->status
        ]);
        return redirect()->back()
            ->with('success','Data pajak berhasil diupdate');
    }

    public function destroy($id)
    {
        Pajak::findOrFail($id)->delete();
        return redirect()->back()
            ->with('success','Data pajak berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $pajak = Pajak::findOrFail($id);
        /*
        jika status ingin diaktifkan
        maka pajak lain dimatikan
        */
        if($pajak->status == 'nonaktif'){
            Pajak::where('status','aktif')
                ->update(['status'=>'nonaktif']);
            $pajak->status = 'aktif';
        }else{
            $pajak->status = 'nonaktif';
        }
        $pajak->save();
        return redirect()->back();
    }
}