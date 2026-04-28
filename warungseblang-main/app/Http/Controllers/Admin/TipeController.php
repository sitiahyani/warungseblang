<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tipe;
use App\Models\Kategori;
use Illuminate\Http\Request;

class TipeController extends Controller
{
    public function index()
    {
        $tipe = Tipe::all();
        $kategori = Kategori::all();

        return view('admin.tipe.index', compact('tipe', 'kategori'));
    }

    public function store(Request $request)
    {
        Tipe::create([
            'id_kategori' => $request->id_kategori,
            'nama_tipe'   => $request->nama_tipe,
        ]);

        return redirect()->back();
    }
}