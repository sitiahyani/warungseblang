<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Hutang;
use App\Models\Piutang;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JurnalExport;

class LaporanController extends Controller
{
    public function jurnal(Request $request)
{
    $query = JurnalUmum::with('detail.akun');

    // FILTER TANGGAL RANGE
    if ($request->dari && $request->sampai) {
        $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
    }

    // FILTER BULAN
    if ($request->bulan) {
        $query->whereMonth('tanggal', $request->bulan);
    }

    // FILTER TAHUN
    if ($request->tahun) {
        $query->whereYear('tanggal', $request->tahun);
    }

    $data = $query->orderBy('tanggal','asc')->get();

    // EXPORT PDF
    if ($request->export == 'pdf') {
        $pdf = Pdf::loadView('admin.laporan.jurnal_pdf', compact('data','request'));
        return $pdf->download('laporan-jurnal.pdf');
    }

    // EXPORT EXCEL
    if ($request->export == 'excel') {
        return Excel::download(new JurnalExport($data), 'laporan-jurnal.xlsx');
    }

    return view('admin.laporan.jurnal', compact('data'));
}
    public function pembelian()
    {
        $data = Pembelian::with('supplier')
                ->orderBy('tanggal','desc')
                ->get();

        return view('admin.laporan.pembelian', compact('data'));
    }
   
    public function hutang()
    {
        $data = Hutang::with('supplier')
                ->orderBy('id_hutang','desc')
                ->get();

        return view('admin.laporan.hutang', compact('data'));
    }

public function labaRugi(Request $request)
{
    $tanggal_awal = $request->tanggal_awal;
    $tanggal_akhir = $request->tanggal_akhir;

    // ================= PENDAPATAN =================
    $pendapatan_usaha = DetailJurnal::whereHas('akun', function ($q) {
            $q->where('jenis_akun', 'pendapatan');
        })
        ->whereHas('jurnal', function ($q) use ($tanggal_awal, $tanggal_akhir) {
            if ($tanggal_awal && $tanggal_akhir) {
                $q->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir]);
            }
        })
        ->sum('kredit');

    // ================= BEBAN =================
    $beban_usaha = DetailJurnal::whereHas('akun', function ($q) {
            $q->where('jenis_akun', 'beban');
        })
        ->whereHas('jurnal', function ($q) use ($tanggal_awal, $tanggal_akhir) {
            if ($tanggal_awal && $tanggal_akhir) {
                $q->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir]);
            }
        })
        ->sum('debit');

    // ================= TOTAL =================
    $pendapatan_lain = 0;
    $beban_lain = 0;

    $total_pendapatan = $pendapatan_usaha + $pendapatan_lain;
    $total_beban = $beban_usaha + $beban_lain;

    $laba_sebelum_pajak = $total_pendapatan - $total_beban;

    $pajak = 0;

    $laba_setelah_pajak = $laba_sebelum_pajak - $pajak;

    return view('admin.laporan.laba_rugi.index', compact(
        'pendapatan_usaha',
        'pendapatan_lain',
        'total_pendapatan',
        'beban_usaha',
        'beban_lain',
        'total_beban',
        'laba_sebelum_pajak',
        'pajak',
        'laba_setelah_pajak'
    ));
}
public function labaRugiPdf(Request $request)
{
    $tanggal_awal = $request->tanggal_awal;
    $tanggal_akhir = $request->tanggal_akhir;

    $tahun = date('Y');
    $tahun_lalu = $tahun - 1;

    // ================= PENDAPATAN =================
    $pendapatan = DetailJurnal::whereHas('akun', function ($q) {
            $q->where('jenis_akun', 'pendapatan');
        })
        ->when($tanggal_awal && $tanggal_akhir, function ($q) use ($tanggal_awal, $tanggal_akhir) {
            $q->whereHas('jurnal', function ($q2) use ($tanggal_awal, $tanggal_akhir) {
                $q2->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir]);
            });
        })
        ->sum('kredit');

    // ================= BEBAN =================
    $beban = DetailJurnal::whereHas('akun', function ($q) {
            $q->where('jenis_akun', 'beban');
        })
        ->when($tanggal_awal && $tanggal_akhir, function ($q) use ($tanggal_awal, $tanggal_akhir) {
            $q->whereHas('jurnal', function ($q2) use ($tanggal_awal, $tanggal_akhir) {
                $q2->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir]);
            });
        })
        ->sum('debit');

    // ================= LABA =================
    $laba_sebelum_pajak = $pendapatan - $beban;

    $pajak = 0;

    $laba_bersih = $laba_sebelum_pajak - $pajak;

    // tahun lalu (sementara 0 biar aman)
    $pendapatan_lalu = 0;
    $beban_lalu = 0;
    $laba_sebelum_pajak_lalu = 0;
    $pajak_lalu = 0;
    $laba_bersih_lalu = 0;

    $pdf = Pdf::loadView('admin.laporan.laba_rugi.pdf', compact(
        'tahun','tahun_lalu',
        'pendapatan','pendapatan_lalu',
        'beban','beban_lalu',
        'laba_sebelum_pajak','laba_sebelum_pajak_lalu',
        'pajak','pajak_lalu',
        'laba_bersih','laba_bersih_lalu'
    ));

    return $pdf->stream('laporan-laba-rugi.pdf');
}

public function posisiKeuangan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $tahun_lalu = $tahun - 1;

        // helper ambil saldo akun (debit - kredit)
        $getSaldo = function ($kode, $tahun) {
            $debit = DetailJurnal::whereHas('jurnal', function ($q) use ($tahun) {
                    $q->whereYear('tanggal', $tahun);
                })
                ->whereHas('akun', function ($q) use ($kode) {
                    $q->where('kode_akun', $kode);
                })
                ->sum('debit');

            $kredit = DetailJurnal::whereHas('jurnal', function ($q) use ($tahun) {
                    $q->whereYear('tanggal', $tahun);
                })
                ->whereHas('akun', function ($q) use ($kode) {
                    $q->where('kode_akun', $kode);
                })
                ->sum('kredit');

            return $debit - $kredit;
        };

        // ================= ASET =================
        $kas = $getSaldo('1101', $tahun);
        $kas_lalu = $getSaldo('1101', $tahun_lalu);

        $giro = $getSaldo('1103', $tahun) ?? 0;
        $giro_lalu = $getSaldo('1103', $tahun_lalu) ?? 0;

        $deposito = $getSaldo('1104', $tahun) ?? 0;
        $deposito_lalu = $getSaldo('1104', $tahun_lalu) ?? 0;

        $piutang = $getSaldo('1102', $tahun);
        $piutang_lalu = $getSaldo('1102', $tahun_lalu);

        $persediaan = $getSaldo('1105', $tahun) ?? 0;
        $persediaan_lalu = $getSaldo('1105', $tahun_lalu) ?? 0;

        $beban_dimuka = $getSaldo('1106', $tahun) ?? 0;
        $beban_dimuka_lalu = $getSaldo('1106', $tahun_lalu) ?? 0;

        $aset_tetap = $getSaldo('1201', $tahun) ?? 0;
        $aset_tetap_lalu = $getSaldo('1201', $tahun_lalu) ?? 0;

        $akumulasi_penyusutan = $getSaldo('1202', $tahun) ?? 0;
        $akumulasi_penyusutan_lalu = $getSaldo('1202', $tahun_lalu) ?? 0;

        // ================= LIABILITAS =================
        $utang_usaha = -$getSaldo('2101', $tahun);
        $utang_usaha_lalu = -$getSaldo('2101', $tahun_lalu);

        $utang_bank = -$getSaldo('2102', $tahun) ?? 0;
        $utang_bank_lalu = -$getSaldo('2102', $tahun_lalu) ?? 0;

        // ================= EKUITAS =================
        $modal = -$getSaldo('3101', $tahun);
        $modal_lalu = -$getSaldo('3101', $tahun_lalu);

        // laba (tahun berjalan)
        $pendapatan = DetailJurnal::whereHas('akun', fn($q)=>$q->where('jenis_akun','pendapatan'))->sum('kredit');
        $beban = DetailJurnal::whereHas('akun', fn($q)=>$q->where('jenis_akun','beban'))->sum('debit');
        $laba = $pendapatan - $beban;

        $laba_lalu = 0; // bisa kamu isi kalau mau historis

        return view('admin.laporan.posisi_keuangan.index', compact(
            'tahun','tahun_lalu',
            'kas','kas_lalu',
            'giro','giro_lalu',
            'deposito','deposito_lalu',
            'piutang','piutang_lalu',
            'persediaan','persediaan_lalu',
            'beban_dimuka','beban_dimuka_lalu',
            'aset_tetap','aset_tetap_lalu',
            'akumulasi_penyusutan','akumulasi_penyusutan_lalu',
            'utang_usaha','utang_usaha_lalu',
            'utang_bank','utang_bank_lalu',
            'modal','modal_lalu',
            'laba','laba_lalu'
        ));
    }

    public function posisiKeuanganPdf(Request $request)
    {
        $data = $this->posisiKeuangan($request)->getData();

        $pdf = Pdf::loadView('admin.laporan.posisi_keuangan.pdf', (array) $data);

        return $pdf->stream('laporan-posisi-keuangan.pdf');
    }

public function calk(Request $request)
{
    $tanggal = $request->tanggal;

    // FILTER TANGGAL (opsional)
    $query = DetailJurnal::with('akun');

    if ($tanggal) {
        $query->whereDate('created_at', '<=', $tanggal);
    }

    // 🔥 ASET
    $kas = $query->clone()->whereHas('akun', fn($q) => $q->where('kode_akun', '1101'))
        ->sum('debit')
        - $query->clone()->whereHas('akun', fn($q) => $q->where('kode_akun', '1101'))
        ->sum('kredit');

    $piutang = $query->clone()->whereHas('akun', fn($q) => $q->where('kode_akun', '1102'))
        ->sum('debit')
        - $query->clone()->whereHas('akun', fn($q) => $q->where('kode_akun', '1102'))
        ->sum('kredit');

    // 🔥 KEWAJIBAN
    $utang = $query->clone()->whereHas('akun', fn($q) => $q->where('kode_akun', '2101'))
        ->sum('kredit')
        - $query->clone()->whereHas('akun', fn($q) => $q->where('kode_akun', '2101'))
        ->sum('debit');

    // 🔥 MODAL
    $modal = $query->clone()->whereHas('akun', fn($q) => $q->where('kode_akun', '3101'))
        ->sum('kredit')
        - $query->clone()->whereHas('akun', fn($q) => $q->where('kode_akun', '3101'))
        ->sum('debit');

    // 🔥 LABA RUGI
    $pendapatan = $query->clone()->whereHas('akun', fn($q) => $q->where('jenis_akun', 'Pendapatan'))
        ->sum('kredit');

    $beban = $query->clone()->whereHas('akun', fn($q) => $q->where('jenis_akun', 'Beban'))
        ->sum('debit');

    $laba = $pendapatan - $beban;

    return view('admin.laporan.calk.index', compact(
        'kas','piutang','utang','modal','pendapatan','beban','laba'
    ));
}

public function calkPdf(Request $request)
{
    $data = $this->calk($request)->getData();

    $pdf = Pdf::loadView('admin.laporan.calk.pdf', (array) $data)
        ->setPaper('A4', 'portrait');

    return $pdf->stream('calk.pdf');
}
}