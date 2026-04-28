<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Pelanggan::orderBy('id_pelanggan','desc')->get();
        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20|unique:pelanggan,no_hp',
            'alamat' => 'nullable|string|max:255'
        ],[
            'no_hp.unique' => 'Nomor HP sudah terdaftar!'
        ]);

        Pelanggan::create($request->all());

        return redirect()->back()->with('success','Berhasil ditambah');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_hp' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('pelanggan','no_hp')->ignore($id,'id_pelanggan')
            ],
            'alamat' => 'nullable|string|max:255'
        ],[
            'no_hp.unique' => 'Nomor HP sudah digunakan pelanggan lain!'
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->all());

        return redirect()->back()->with('success','Berhasil update');
    }
    
    public function destroy($id)
    {
        Pelanggan::findOrFail($id)->delete();

        return redirect()->back()->with('success','Berhasil hapus');
    }

    public function simpan(Request $request)
    {
        try {
            $request->validate([
                'nama_pelanggan' => 'required|string|max:100',
                'no_hp' => 'nullable|string|max:20|unique:pelanggan,no_hp',
                'alamat' => 'nullable|string|max:255'
            ], [
                'no_hp.unique' => 'Nomor HP sudah terdaftar!'
            ]);

            $pelanggan = Pelanggan::create([
                'nama_pelanggan' => $request->nama_pelanggan,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil ditambahkan',
                'data' => $pelanggan
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cari(Request $request)
    {
        $keyword = $request->keyword;

        $data = Pelanggan::where('nama_pelanggan', 'LIKE', "%{$keyword}%")
            ->orWhere('no_hp', 'LIKE', "%{$keyword}%")
            ->select('id_pelanggan', 'nama_pelanggan', 'no_hp')
            ->limit(10)
            ->get();

        dd($data);
    }
}