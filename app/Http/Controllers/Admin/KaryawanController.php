<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::all();
        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function store(Request $request)
    {
        // VALIDASI DASAR KARYAWAN
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:30',
            'jenis_kelamin' => 'nullable|in:L,P',
            'email' => 'nullable|email|max:255',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'role' => 'nullable|in:admin,kasir,pelayan',
        ]);

        // Upload foto (opsional)
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('karyawan', 'public');
        }

        // Simpan data karyawan
        $karyawan = Karyawan::create([
            'nama_karyawan'  => $request->nama_karyawan,
            'no_hp'          => $request->no_hp,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'email'          => $request->email,
            'foto'           => $fotoPath,
            'tanggal_masuk'  => now()->toDateString(),
            'tanggal_keluar' => null,
            'status'         => 'aktif',
        ]);

        // ==============================
        // BUAT USER JIKA ROLE DIPILIH
        // ==============================
        if ($request->role) {

            $request->validate([
                'username' => 'required|unique:users,username',
                'password' => 'required|min:4',
            ]);

            User::create([
                'nama'        => $request->nama_karyawan,
                'username'    => $request->username,
                'password'    => Hash::make($request->password),
                'role'        => $request->role,
                'id_karyawan' => $karyawan->id_karyawan, // sesuaikan dengan PK kamu
            ]);
        }

        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function toggleStatus($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $karyawan->status = $karyawan->status == 'aktif'
            ? 'nonaktif'
            : 'aktif';

        $karyawan->save();

        return redirect()->back();
    }
}