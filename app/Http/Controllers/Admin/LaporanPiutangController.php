<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HistoriPembayaranHutang;
use Carbon\Carbon;
use App\Exports\LaporanPiutangExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf; 
class LaporanPiutangController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->baseQuery($request);

        $data = $this->groupData($query->get());

        // 🔥 filter status setelah grouping
        $data = $this->filterStatus($data, $request);

        return view('admin.laporan_transaksi.laporan_piutang', compact('data'));
    }

    public function exportPdf(Request $request)
    {
        $query = $this->baseQuery($request);

        $data = $this->groupData($query->get());

        // 🔥 filter status juga di PDF
        $data = $this->filterStatus($data, $request);

        $pdf = PDF::loadView('admin.laporan_transaksi.pdf_piutang', compact('data'));
        return $pdf->download('laporan-piutang.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->filterStatus(
            $this->groupData($this->baseQuery($request)->get()),
            $request
        );

        return Excel::download(
            new LaporanPiutangExport($data),
            'laporan-piutang.xlsx'
        );
    }

    // 🔥 QUERY DASAR (BIAR TIDAK DUPLIKAT)
    private function baseQuery($request)
    {
        $query = HistoriPembayaranHutang::with('penjualan.pelangganRel');

        // 📅 filter tanggal
        if ($request->tanggal) {
            [$start, $end] = explode(' - ', $request->tanggal);

            $query->whereBetween('tanggal', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        // 🔍 search (kode + pelanggan jadi 1)
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('penjualan', function ($q2) use ($search) {
                    $q2->where('kode_transaksi', 'like', "%$search%")
                       ->orWhere('nama_pelanggan', 'like', "%$search%");
                })
                ->orWhereHas('penjualan.pelangganRel', function ($q2) use ($search) {
                    $q2->where('nama_pelanggan', 'like', "%$search%");
                });
            });
        }

        return $query;
    }

    // 🔥 GROUPING DATA
    private function groupData($collection)
    {
        return $collection
            ->groupBy('id_penjualan')
            ->map(function ($items) {

                $first = $items->first();
                $penjualan = $first->penjualan;

                $total = $penjualan->total ?? 0;
                $totalBayar = $items->sum('jumlah_bayar');
                $sisa = $total - $totalBayar;

                return (object)[
                    'tanggal' => $penjualan->tanggal,
                    'kode_transaksi' => $penjualan->kode_transaksi ?? '-',
                    'pelanggan' => $penjualan->pelangganRel->nama_pelanggan 
                                    ?? $penjualan->nama_pelanggan 
                                    ?? '-',
                    'total' => $total,

                    // 🔥 INI YANG DIPERBAIKI
                    'terbayar' => $totalBayar,

                    'sisa' => $sisa,
                    'status' => $sisa <= 0 ? 'Lunas' : 'Belum',
                ];
            })
            ->values();
    }

    // 🔥 FILTER STATUS (SETELAH GROUPING)
    private function filterStatus($data, $request)
    {
        if ($request->status) {
            return $data->filter(function ($item) use ($request) {
                if ($request->status == 'lunas') {
                    return $item->status == 'Lunas';
                } else {
                    return $item->status == 'Belum';
                }
            })->values();
        }

        return $data;
    }
}