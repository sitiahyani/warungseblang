<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\HistoriPembayaranHutang;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        // DEFAULT FILTER = HARI INI
        if (!$request->filled('tanggal')) {
            $today = now()->format('Y-m-d');
            $request->merge([
                'tanggal' => $today . ' - ' . $today
            ]);
        }

        $penjualan = Penjualan::with([
            'pelangganRel',
            'kategoriRel',
            'tipeRel',
            'userRel',        // 🔥 kasir
            'karyawanRel',    // 🔥 nama kasir (kalau pakai karyawan)
            'detailLayanan.layanan', // 🔥 wedding
            'details.barang', // 🔥 resto
            'tipeRel'         // 🔥 tipe acara
        ])->orderBy('tanggal', 'desc');

        // PESANAN PENDING DARI PELAYAN
        $pesananPending = Penjualan::with([
                'details.barang',
                'pelangganRel'
            ])
            ->where('status', 'pending')
            ->where('sumber_transaksi', 'pelayan')
            ->orderBy('tanggal', 'asc')
            ->get();

        // RIWAYAT TRANSAKSI (RESTO + WEDDING + HOMESTAY)
        $query = Penjualan::with([
            'details.barang',
            'detailLayanan.layanan',
            'pelangganRel',
            'kategoriRel',
            'tipeRel',
            'userRel',
            'karyawanRel',
            'historiPembayaran'
        ])
        ->withCount('details')
        ->whereIn('status', ['lunas', 'belum']);

        /*
        |--------------------------------------------------------------------------
        | FILTER TANGGAL
        |--------------------------------------------------------------------------
        */
        if ($request->filled('tanggal')) {
            $range = explode(' - ', $request->tanggal);

            if (count($range) == 2) {
                $start = trim($range[0]);
                $end   = trim($range[1]);

                $query->whereBetween('tanggal', [
                    $start . ' 00:00:00',
                    $end . ' 23:59:59'
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER METODE BAYAR
        |--------------------------------------------------------------------------
        */
        if ($request->filled('metode_bayar') && $request->metode_bayar != 'semua') {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $penjualan = $query->orderBy('tanggal', 'desc')->get();

        /*
        |--------------------------------------------------------------------------
        | FORMAT DATA RIWAYAT
        |--------------------------------------------------------------------------
        */
        foreach ($penjualan as $p) {
            $totalBayar = $p->bayar ?? 0;

            $p->sisa_fix = max($p->total - $totalBayar, 0);
            $p->status_fix = $p->sisa_fix <= 0 ? 'lunas' : 'belum';

            $p->nama_pelanggan_fix = $p->pelangganRel->nama_pelanggan
                ?? $p->pelanggan
                ?? '-';

            /*
            |--------------------------------------------------------------------------
            | AUTO KATEGORI
            |--------------------------------------------------------------------------
            */
            if (str_starts_with($p->kode_transaksi, 'WD-')) {
                $p->kategori = 'Wedding';
                $p->items_label = 'Paket Wedding';
            } elseif (str_starts_with($p->kode_transaksi, 'HS-')) {
                $p->kategori = 'Homestay';
                $p->items_label = 'Booking Homestay';
            } else {
                $p->kategori = 'Resto';
                $p->items_label = $p->details_count . ' item';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | HISTORI CICILAN
        |--------------------------------------------------------------------------
        */
        $historiQuery = HistoriPembayaranHutang::with([
                'penjualan.pelangganRel'
            ]);

        if ($request->filled('tanggal')) {
            $range = explode(' - ', $request->tanggal);

            if (count($range) == 2) {
                $start = trim($range[0]);
                $end   = trim($range[1]);

                $historiQuery->whereBetween('tanggal_bayar', [
                    $start . ' 00:00:00',
                    $end . ' 23:59:59'
                ]);
            }
        }

        $historiDb = $historiQuery
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        $historiCicilan = collect();

        foreach ($historiDb as $histori) {
            if (!$histori->penjualan) {
                continue;
            }

            $trx = $histori->penjualan;

            $historiCicilan->push((object) [
                'kode' => $trx->kode_transaksi ?? '-',
                'tanggal_bayar' => $histori->tanggal_bayar,
                'pelanggan' => $trx->pelangganRel->nama_pelanggan
                    ?? $trx->pelanggan
                    ?? '-',
                'jumlah_bayar' => $histori->jumlah_bayar
            ]);
        }


        $pembelian = Pembelian::with(['supplier','detailPembelian.bahan'])
            ->when($request->tanggal, function ($q) use ($request) {
                $tanggal = explode(' - ', $request->tanggal);

                if (count($tanggal) == 2) {
                    $start = $tanggal[0] . ' 00:00:00';
                    $end   = $tanggal[1] . ' 23:59:59';

                    $q->whereBetween('tanggal', [$start, $end]);
                }
            })
            ->when($request->metode_bayar && $request->metode_bayar != 'semua', function ($q) use ($request) {
                $q->where('metode_bayar', $request->metode_bayar);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('kasir.riwayat.index', compact(
            'penjualan',
            'pembelian',
            'historiCicilan',
            'pesananPending'
        ));
    }

    public function bayarPending(Request $request, $id)
    {
        $request->validate([
            'bayar'   => 'required|numeric|min:1',
            'metode'  => 'required|string'
        ]);

        $trx = Penjualan::with([
            'details.barang',
            'historiPembayaran'
        ])->findOrFail($id);

        $bayarInput = (int) $request->bayar;
        $total = (int) $trx->total;

        /*
        |--------------------------------------------------------------------------
        | TOTAL SUDAH DIBAYAR
        |--------------------------------------------------------------------------
        */
        $totalHistori = $trx->historiPembayaran->sum('jumlah_bayar');
        $bayarAwal = $trx->bayar ?? 0;
        $totalBayar = $bayarAwal + $totalHistori;

        $sisaSebelum = $total - $totalBayar;

        if ($bayarInput > $sisaSebelum) {
            return back()->with('error', 'Pembayaran melebihi sisa hutang!');
        }

        $sisaSesudah = $sisaSebelum - $bayarInput;

        $bayarInput = min($bayarInput, $sisaSebelum);
        /*
        |--------------------------------------------------------------------------
        | RESTO / DARI PELAYAN HARUS FULL
        |--------------------------------------------------------------------------
        */
        if ($trx->sumber_transaksi === 'pelayan') {
            if ($bayarInput < $sisaSebelum) {
                return back()->with('error', 'Pesanan dari pelayan wajib lunas.');
            }

            $trx->bayar = $total;
            $trx->metode_bayar = $request->metode;
            $trx->status = 'lunas';
            $trx->save();

            foreach ($trx->details as $detail) {
                if ($detail->barang) {
                    $detail->barang->decrement('stok', $detail->qty);
                }
            }

            return redirect()
                ->route('kasir.riwayat')
                ->with('success', 'Pembayaran resto berhasil.');
        }

        /*
        |--------------------------------------------------------------------------
        | CICILAN KREDIT KASIR
        |--------------------------------------------------------------------------
        */
        if ($bayarInput < 50000) {
            return back()->with('error', 'Minimal pembayaran Rp 50.000');
        }

        $sisaSesudah = $sisaSebelum - $bayarInput;

        // jika sisa kurang dari 100rb wajib lunas
        if ($sisaSesudah > 0 && $sisaSesudah < 100000) {
            return back()->with(
                'error',
                'Sisa pembayaran di bawah Rp 100.000 harus dilunasi.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN HISTORI CICILAN + SISA HUTANG
        |--------------------------------------------------------------------------
        */
        HistoriPembayaranHutang::create([
            'id_penjualan' => $trx->id_penjualan,
            'tanggal_bayar' => now(),
            'jumlah_bayar' => $bayarInput,
            'sisa_hutang' => max($sisaSesudah, 0)
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS TRANSAKSI
        |--------------------------------------------------------------------------
        */
        $trx->status = $sisaSesudah <= 0 ? 'lunas' : 'belum';
        $trx->metode_bayar = $request->metode;
        $trx->save();

        return redirect()
            ->route('kasir.riwayat')
            ->with('success', 'Pembayaran cicilan berhasil.');
    }

    public function pesanan()
    {
        $pesananPending = Penjualan::with([
            'details.barang'
        ])
        ->where('status', 'pending')
        ->where('sumber_transaksi', 'pelayan')
        ->orderBy('id_penjualan', 'desc')
        ->get();

        return view('kasir.pesanan.index', compact('pesananPending'));
    }

    public function cetakDapur($id)
    {
        $pesanan = \App\Models\Penjualan::with('details.barang')
            ->findOrFail($id);

        $pdf = Pdf::loadView('kasir.pesanan.cetak_dapur', compact('pesanan'));

        return $pdf->stream('bon-dapur.pdf');
    }
}