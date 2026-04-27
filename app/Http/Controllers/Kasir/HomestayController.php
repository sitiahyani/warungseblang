<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\Penjualan;
use App\Models\DetailPenjualanLayanan;
use App\Models\JurnalUmum; // ✅ TAMBAHAN
use App\Models\DetailJurnal; // ✅ TAMBAHAN
use App\Models\KodeAkun; // ✅ TAMBAHAN
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomestayController extends Controller
{
    // ================= HALAMAN =================
    public function index()
    {
        $kategori = DB::table('kategori')
            ->where('nama_kategori','homestay')
            ->first();

        $layanan = Layanan::where('id_kategori', $kategori->id_kategori)->get();

        $bookingData = DB::table('penjualan')
        ->join('detail_penjualan_layanan', 'penjualan.id_penjualan', '=', 'detail_penjualan_layanan.id_penjualan')
        ->join('layanan', 'detail_penjualan_layanan.id_layanan', '=', 'layanan.id_layanan')
        ->whereNotNull('detail_penjualan_layanan.tanggal_checkin')
        ->select(
            'detail_penjualan_layanan.tanggal_checkin',
            'detail_penjualan_layanan.tanggal_checkout',
            'detail_penjualan_layanan.id_layanan',
            'layanan.nama_layanan'
        )
        ->get();

        $bookings = $bookingData;
        $bookingGrouped = [];

        foreach ($bookingData as $item) {

            $start = Carbon::parse($item->tanggal_checkin)->startOfDay();
            $end   = Carbon::parse($item->tanggal_checkout)->startOfDay();

            $key = (string) $item->id_layanan;

            if (!isset($bookingGrouped[$key])) {
                $bookingGrouped[$key] = [];
            }

            $current = $start->copy();

            while ($current->lt($end)) {

                $tgl = $current->format('Y-m-d');

                if (!collect($bookingGrouped[$key])->contains('date', $tgl)) {
                    $bookingGrouped[$key][] = [
                        'date' => $tgl,
                        'type' => 'full'
                    ];
                }

                $current->addDay();
            }

            $checkoutDay = $end->format('Y-m-d');

            $exists = collect($bookingGrouped[$key])
                ->firstWhere('date', $checkoutDay);

            if (!$exists) {
                $bookingGrouped[$key][] = [
                    'date' => $checkoutDay,
                    'type' => 'checkout'
                ];
            }
        }

        $tipe = DB::table('tipe')
            ->where('id_kategori', $kategori->id_kategori)
            ->get();
      
        return view('kasir.penjualan.homestay', compact(
            'layanan',
            'bookingGrouped',
            'tipe',
            'bookings'
        ));
    }

    // ================= SIMPAN =================
    public function simpan(Request $request)
    {
        $request->validate([
            'id_layanan' => 'required',
            'nama_pelanggan' => 'nullable',
            'bayar' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {

            $idPelanggan = $request->id_pelanggan;
            $namaInput = $request->nama_pelanggan;

            $namaPelanggan = $idPelanggan 
                ? null 
                : ($namaInput ? $namaInput : 'Umum');

            $checkin = Carbon::parse($request->tanggal_checkin)
                ->setTime(now()->hour, now()->minute);

            $checkout = Carbon::parse($request->tanggal_checkin)
                ->addDay()
                ->setTime(12, 0);

            if ($checkout <= $checkin) {
                return back()->with('error', 'Tanggal tidak valid');
            }

            $today = Carbon::today();
            if ($checkin->lt($today)) {
                return back()->with('error', 'Tidak bisa booking di tanggal yang sudah lewat');
            }

            $tanggalInput = Carbon::parse($request->tanggal_checkin)->startOfDay();

            if ($tanggalInput->lt($today)) {
                return back()->with('error', 'Tidak bisa booking di tanggal yang sudah lewat');
            }

            $existingBooking = DB::table('detail_penjualan_layanan')
                ->where('id_layanan', $request->id_layanan)
                ->where(function($query) use ($checkin, $checkout) {
                    $query->where('tanggal_checkin', '<', $checkout)
                        ->where('tanggal_checkout', '>', $checkin);
                })
                ->first();

            if ($existingBooking) {
                return redirect()->back()
                    ->with('error', 'Transaksi gagal, kamar sudah terisi')
                    ->withInput();
            }

            $layanan = Layanan::findOrFail($request->id_layanan);
            $idTipe = $layanan->id_tipe;
            $total = $layanan->harga;

            $bayar = $request->bayar;

            if ($bayar < $total) {
                return back()->with('error','Pembayaran kurang');
            }

            $user = Auth::user();
            $idKaryawan = $user->id_karyawan ?? null;

            $idShift = DB::table('shift')
                ->where('status', 1)
                ->value('id_shift');

            if(!$idShift){
                return back()->with('error','Shift belum aktif');
            }

            $idKategori = DB::table('kategori')
                ->where('nama_kategori', 'homestay')
                ->value('id_kategori');

            // ================= SIMPAN =================
            $trx = Penjualan::create([
                'kode_transaksi' => 'HS-' . date('YmdHis'),
                'tanggal' => now(),
                'id_karyawan' => $idKaryawan,
                'id_shift' => $idShift,
                'id_kategori' => $idKategori,
                'id_tipe' => $idTipe,
                'id_pelanggan' => $idPelanggan,
                'nama_pelanggan' => $namaPelanggan,
                'id_user' => Auth::id(),
                'total' => $total,
                'bayar' => $bayar,
                'status' => 'lunas',
                'keterangan' => $request->keterangan,
                'metode_bayar' => $request->metode_bayar,
                'sumber_transaksi' => 'kasir'
            ]);

            DetailPenjualanLayanan::create([
                'id_penjualan' => $trx->id_penjualan,
                'id_layanan' => $request->id_layanan,
                'harga' => $layanan->harga,
                'tanggal_checkin' => $checkin,
                'tanggal_checkout' => $checkout,
                'durasi' => 1,
                'subtotal' => $layanan->harga,
            ]);

            // =================================================
            // 🔥 TAMBAHAN JURNAL UMUM (TANPA UBAH STRUKTUR)
            // =================================================

            $jurnal = JurnalUmum::create([
                'tanggal' => now(),
                'keterangan' => 'Pendapatan Homestay - ' . $trx->kode_transaksi,
                'sumber' => 'penjualan',
                'ref_id' => $trx->id_penjualan
            ]);

            $akunKas = KodeAkun::where('kode_akun', '1101')->first();
            $akunPendapatan = KodeAkun::where('kode_akun', '4102')->first();

            if (!$akunKas || !$akunPendapatan) {
                throw new \Exception('Kode akun 1101 / 4102 belum ada');
            }

            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun' => $akunKas->id_akun,
                'debit' => $total,
                'kredit' => 0,
            ]);

            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun' => $akunPendapatan->id_akun,
                'debit' => 0,
                'kredit' => $total,
            ]);

            // =================================================

            DB::commit();

            return redirect()->back()->with([
                'success' => 'Booking berhasil',
                'struk' => [
                    'kode' => $trx->kode_transaksi,
                    'nama' => $namaPelanggan ?? 'Pelanggan Terdaftar',
                    'total' => $trx->total,
                    'bayar' => $trx->bayar,
                    'kembalian' => $trx->bayar - $trx->total,
                    'kamar' => $layanan->nama_layanan,
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}