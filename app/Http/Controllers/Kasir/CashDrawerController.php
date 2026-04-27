<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\CashDrawer;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashDrawerController extends Controller
{
    public function index()
    {
        // 🔥 FIX: pakai default auth
        $kasir = Auth::user();

        if (!$kasir) {
            return redirect()->route('login');
        }

        // 🔥 FIX: karena PK kamu id_user
        $id_user = $kasir->id_user;

        // 🔥 ambil drawer aktif
        $drawer = CashDrawer::with('shift')
            ->where('id_user', $id_user)
            ->whereNull('cash_akhir')
            ->latest('id_drawer')
            ->first();

        $shift = $drawer?->shift;

        $shiftAktif = $drawer ? true : false;
        $shiftSelesai = false;

        if ($drawer && $shift) {
            $now = Carbon::now()->format('H:i:s');

            if ($now >= $shift->waktu_mulai && $now <= $shift->waktu_selesai) {
                $shiftAktif = true;
            } else {
                $shiftSelesai = true;
            }
        }

        return view('kasir.cashdrawer.index', [
            'shift' => $shift,
            'drawer' => $drawer,
            'karyawan' => $kasir,
            'namaKasir' => $kasir->nama ?? $kasir->username ?? 'Kasir',
            'shiftAktif' => $shiftAktif,
            'shiftSelesai' => $shiftSelesai
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:0'
        ]);

        $kasir = Auth::user();

        $drawer = CashDrawer::where('id_user', $kasir->id_user)
            ->whereNull('cash_akhir')
            ->latest('id_drawer')
            ->first();

        if (!$drawer) {
            return back()->with('error', 'Tidak ada shift aktif');
        }

        // ✅ CASH AWAL (WAJIB PERTAMA)
        if ($drawer->cash_awal == 0) {
            $drawer->update([
                'cash_awal' => $request->nominal
            ]);

            return back()->with('success', 'Cash awal disimpan');
        }

        // ✅ CASH AKHIR
        $drawer->update([
            'cash_akhir' => $request->nominal,
            'selisih' => $request->nominal - $drawer->cash_awal
        ]);

        return back()->with('success', 'Cash akhir disimpan');
    }

    public function bukaShift()
    {
        $kasir = Auth::user();
        $today = now()->toDateString();

        // ❌ masih ada shift aktif
        $drawerAktif = CashDrawer::where('id_user', $kasir->id_user)
            ->whereNull('cash_akhir')
            ->exists();

        if ($drawerAktif) {
            return back()->with('error', 'Masih ada shift aktif');
        }

        // 🔥 ambil shift berdasarkan jam SEKARANG dari tabel
        $now = now()->format('H:i:s');

        $shift = Shift::where('waktu_mulai', '<=', $now)
            ->where('waktu_selesai', '>=', $now)
            ->first();

        if (!$shift) {
            return back()->with('error', 'Tidak ada shift sekarang');
        }

        // ❗ cek shift sudah dipakai oleh kasir ini hari ini
        $sudahDipakai = CashDrawer::where('id_user', $kasir->id_user)
            ->where('id_shift', $shift->id_shift)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($sudahDipakai) {
            return back()->with('error', 'Shift ini sudah kamu pakai hari ini');
        }

        // ✅ create drawer
        CashDrawer::create([
            'nama_drawer' => 'Drawer ' . $kasir->username,
            'id_user' => $kasir->id_user,
            'id_shift' => $shift->id_shift,
            'tanggal' => $today,
            'cash_awal' => 0,
            'cash_akhir' => null,
            'selisih' => 0
        ]);

        return redirect()->route('cashdrawer')
            ->with('success', 'Shift berhasil dibuka');
    }

    public function tutupShift()
    {
        $kasir = Auth::user();

        $drawer = CashDrawer::where('id_user', $kasir->id_user)
            ->whereNull('cash_akhir')
            ->latest('id_drawer')
            ->first();

        if (!$drawer) {
            return back()->with('error', 'Tidak ada shift aktif');
        }

        if (is_null($drawer->cash_awal)) {
            return back()->with('error', 'Isi cash awal dulu');
        }

        if (is_null($drawer->cash_akhir)) {
            return back()->with('error', 'Isi cash akhir dulu');
        }

        // 👉 sebenarnya sudah selesai, tapi kita bisa update status jika ada field
        return redirect()->route('cashdrawer')
            ->with('success', 'Shift ditutup');
    }
}