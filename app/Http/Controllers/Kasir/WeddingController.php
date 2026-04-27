<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Penjualan;
use App\Models\HistoriPembayaranHutang;
use App\Models\Kategori;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeddingController extends Controller
{
    public function index()
    {
        $kategoriWedding = DB::table('kategori')
            ->whereRaw('LOWER(nama_kategori) LIKE ?', ['%wedding%'])
            ->first();

        if (!$kategoriWedding) {
            abort(404, 'Kategori wedding tidak ditemukan di database!');
        }

        $layanan = DB::table('layanan')
            ->where('id_kategori', $kategoriWedding->id_kategori)
            ->where('status', 'aktif')
            ->get();

        // 🔥 PAKAI DB QUERY BUILDER (TANPA RELASI MODEL)
        $bookings = DB::table('penjualan')
            ->leftJoin('tipe', 'penjualan.id_tipe', '=', 'tipe.id_tipe')
            ->whereNotNull('penjualan.tanggal_acara')
            ->where('penjualan.sumber_transaksi', 'kasir')
            ->whereIn('penjualan.status', ['lunas', 'belum'])
            ->select('penjualan.tanggal_acara', 'tipe.nama_tipe')
            ->get();

        // 🔥 FORMAT BOOKING DATES
        $bookingDates = [];
        foreach($bookings as $booking) {
            $date = \Carbon\Carbon::parse($booking->tanggal_acara)->format('Y-m-d');
            $tipe = strtolower($booking->nama_tipe ?? '');
            
            if (!isset($bookingDates[$date])) {
                $bookingDates[$date] = [];
            }
            
            if ($tipe == 'indor' || $tipe == 'outdor') {
                $bookingDates[$date][] = $tipe;
            }
        }

        $tipe = DB::table('tipe')
            ->where('id_kategori', $kategoriWedding->id_kategori)
            ->get();

        return view('kasir.penjualan.wedding', compact('layanan', 'bookingDates', 'tipe'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan'   => 'required|exists:pelanggan,id_pelanggan',
            'tanggal_acara'  => 'required|date|after_or_equal:today',
            'jam_acara'      => 'required',
            'harga_paket'    => 'required|numeric',
            'id_layanan'     => 'required|exists:layanan,id_layanan',
            'id_tipe'        => 'required|exists:tipe,id_tipe',
            'nominal_bayar'  => 'nullable|numeric|min:0',
            'metode_bayar'   => 'required',
        ]);

        DB::beginTransaction();

        try {
            // 🔥 CEK APAKAH TIPE SUDAH DIBOOKING DI TANGGAL TERSEBUT
            $tipeData = DB::table('tipe')->where('id_tipe', $request->id_tipe)->first();
            $namaTipe = strtolower($tipeData->nama_tipe ?? '');
            
            if (!in_array($namaTipe, ['indor', 'outdor'])) {
                throw new \Exception('Tipe acara harus indoor atau outdoor');
            }

            // Cek booking yang sudah ada di tanggal tersebut
            $existingBookings = Penjualan::where('tanggal_acara', $request->tanggal_acara)
                ->where('sumber_transaksi', 'kasir')
                ->whereIn('status', ['lunas', 'belum'])
                ->whereHas('tipe', function($query) use ($namaTipe) {
                    $query->whereRaw('LOWER(nama_tipe) = ?', [$namaTipe]);
                })
                ->exists();

            if ($existingBookings) {
                $errorMessage = $namaTipe == 'indor' 
                    ? "❌ Booking GAGAL! Indoor sudah dibooking pada tanggal " . date('d/m/Y', strtotime($request->tanggal_acara))
                    : "❌ Booking GAGAL! Outdoor sudah dibooking pada tanggal " . date('d/m/Y', strtotime($request->tanggal_acara));
                
                return back()->withInput()->with('error', $errorMessage);
            }

            $pelanggan = Pelanggan::findOrFail($request->id_pelanggan);

            $total = (int) $request->harga_paket;
            $bayar = (int) ($request->nominal_bayar ?? 0);

            $metodeBayar = strtolower($request->metode_bayar);
            if ($metodeBayar === 'cash') {
                $metodeBayar = 'tunai';
            }

            $status = $bayar >= $total ? 'lunas' : 'belum';
            $sisaHutang = max($total - $bayar, 0);

            $kodeTransaksi = $this->generateKodeWedding();

            $kategoriWedding = Kategori::whereRaw('LOWER(nama_kategori) LIKE ?', ['%wedding%'])
                ->first();

            if (!$kategoriWedding) {
                throw new \Exception('Kategori wedding tidak ditemukan saat simpan');
            }

          $user = Auth::user();

            if (!$user) {
                return back()->with('error', 'Kamu belum login!');
            }

           $id_user = $user->id;
            $id_karyawan = $user->id_karyawan;

            // 🔥 INI WAJIB ADA SEBELUM DIPAKAI
           $id_shift = DB::table('shift')
                ->where('status', 1)
                ->value('id_shift');

            if (!$id_shift) {
                return back()->withInput()->with('error', '❌ Shift belum dibuka!');
            }
                
            
            $penjualan = Penjualan::create([
                'kode_transaksi' => $kodeTransaksi,
                'tanggal' => now(),
                'tanggal_acara' => $request->tanggal_acara,
                'jam_acara' => $request->jam_acara,
                'id_kategori' => $kategoriWedding->id_kategori, 
                'id_tipe' => $request->id_tipe,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_user' => $id_user,
                'id_karyawan' => $id_karyawan,
                'id_shift' => $id_shift,
                'nama_pelanggan' => $pelanggan->nama_pelanggan,
                'total' => $total,
                'bayar' => $bayar,
                'sisa_hutang' => $sisaHutang,
                'metode_bayar' => $metodeBayar,
                'status' => $status,
                'keterangan' => $request->keterangan,
                'sumber_transaksi' => 'kasir',
            ]);

            DB::table('detail_penjualan_layanan')->insert([
                'id_penjualan' => $penjualan->id_penjualan,
                'id_layanan'   => $request->id_layanan,
                'durasi'       => 1,
                'harga'        => $total,
                'subtotal'     => $total,
            ]);

            if ($sisaHutang > 0) {
                HistoriPembayaranHutang::create([
                    'id_penjualan'  => $penjualan->id_penjualan,
                    'tanggal_bayar' => now(),
                    'jumlah_bayar'  => $bayar,
                    'sisa_hutang'   => $sisaHutang,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('penjualan.wedding')
                ->with('success', 'Pesanan wedding berhasil disimpan')
                ->with('print_data', [
                    'kode' => $penjualan->kode_transaksi,
                    'pelanggan' => $penjualan->nama_pelanggan,
                    'tanggal' => $penjualan->tanggal,
                    'tanggal_acara' => $penjualan->tanggal_acara,
                    'total' => $penjualan->total,
                    'bayar' => $penjualan->bayar,
                    'sisa' => $penjualan->sisa_hutang,
                    'status' => $penjualan->status,
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function generateKodeWedding()
    {
        $tanggal = now()->format('Ymd');

        $last = Penjualan::where('kode_transaksi', 'like', 'WD-' . $tanggal . '%')
            ->latest('id_penjualan')
            ->first();

        $nomor = 1;

        if ($last) {
            $lastNumber = (int) substr($last->kode_transaksi, -4);
            $nomor = $lastNumber + 1;
        }

        return 'WD-' . $tanggal . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }

    public function cariPelanggan(Request $request)
    {
        $keyword = $request->keyword;

        $data = Pelanggan::where('nama_pelanggan', 'LIKE', "%{$keyword}%")
            ->orWhere('no_hp', 'LIKE', "%{$keyword}")
            ->select('id_pelanggan', 'nama_pelanggan', 'no_hp')
            ->limit(10)
            ->get();

        return response()->json($data);
    }
}