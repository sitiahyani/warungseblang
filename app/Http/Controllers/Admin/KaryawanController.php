<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KaryawanExport;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::all();
        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'no_hp' => 'required|unique:karyawan,no_hp',
            'jenis_kelamin' => 'nullable|in:L,P',
            'email' => 'nullable|email|max:255',
            'jabatan' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'role' => 'nullable|in:admin,kasir,pelayan'
        ]);

        DB::beginTransaction();

        try {
            // upload foto
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('karyawan', 'public');
            }

            // simpan karyawan
            $karyawan = Karyawan::create([
                'nama_karyawan'  => $request->nama_karyawan,
                'no_hp'          => $request->no_hp,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'email'          => $request->email,
                'jabatan'        => $request->jabatan,
                'foto'           => $fotoPath,
                'tanggal_masuk'  => now()->toDateString(),
                'tanggal_keluar' => null,
                'status'         => 'aktif',
            ]);

            // buat user kalau ada role
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
                    'id_karyawan' => $karyawan->id_karyawan,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Karyawan berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            // hapus foto kalau gagal
            if (!empty($fotoPath) && Storage::exists('public/'.$fotoPath)) {
                Storage::delete('public/'.$fotoPath);
            }

            return back()->with('error', 'Gagal: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'no_hp' => 'required|unique:karyawan,no_hp,' . $id . ',id_karyawan',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => 'required|email|max:255',
            'jabatan' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $data = [
                'nama_karyawan' => $request->nama_karyawan,
                'no_hp' => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'email' => $request->email,
                'jabatan' => $request->jabatan,
            ];

            if ($request->hasFile('foto')) {

                if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                    Storage::disk('public')->delete($karyawan->foto);
                }

                $data['foto'] = $request->file('foto')->store('karyawan', 'public');
            }

            $karyawan->update($data);
            DB::commit();
            return back()->with('success','Data berhasil diupdate');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error','Gagal update: '.$e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->status = $karyawan->status == 'aktif'
            ? 'nonaktif'
            : 'aktif';
        $karyawan->save();

        return back()->with('success','Status berhasil diubah');
    }

    public function exportPdf()
    {
        $karyawan = Karyawan::all();

        $pdf = Pdf::loadView('admin.karyawan.pdf', compact('karyawan'));

        return $pdf->download('data_karyawan.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new KaryawanExport, 'data_karyawan.xlsx');
    }
}