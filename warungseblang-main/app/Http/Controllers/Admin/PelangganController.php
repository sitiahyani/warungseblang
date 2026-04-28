<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{

    public function simpan(Request $request)
    {

        try {

            // validasi input
            $request->validate([
                'nama_pelanggan' => 'required|string|max:100',
                'no_hp' => 'nullable|string|max:20',
                'alamat' => 'nullable|string|max:255'
            ]);

            // simpan pelanggan
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

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ]);

        }

    }

}