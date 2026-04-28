@extends('layouts.admin')

@section('title', 'Laporan Pajak')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">

        <!-- HEADER -->
        <div class="card-header bg-white border-0 py-3">
            <h4 class="mb-0">📊 Laporan Pajak Penjualan Restoran</h4>
        </div>

        <!-- ACTION -->
        <div class="d-flex justify-content-end mb-3 px-3">
            <a href="{{ route('laporan.pajak.pdf', request()->all()) }}" class="btn btn-danger me-2">
                Cetak PDF
            </a>

            <a href="{{ route('laporan.pajak.excel', request()->all()) }}" class="btn btn-success">
                Export Excel
            </a>
        </div>

        <!-- FILTER -->
        <form method="GET" class="px-3">
            <div class="card mb-3 p-3">
                <div class="row">

                    <div class="col-md-4">
                        <label>Rentang Tanggal</label>
                        <input type="text"
                               name="tanggal"
                               id="tanggal"
                               class="form-control"
                               value="{{ request('tanggal') }}"
                               placeholder="YYYY-MM-DD - YYYY-MM-DD">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            Terapkan
                        </button>
                    </div>

                </div>
            </div>
        </form>

        <!-- TABLE -->
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Kategori</th>
                            <th class="text-primary">Harga (DPP)</th>
                            <th class="text-success">Pajak</th>
                            <th class="text-dark">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $totalDpp = 0;
                            $totalPajak = 0;
                        @endphp

                        @forelse($data as $d)
                        @php
                            // ambil persen pajak (misal 10%)
                            $persen = is_object($d->pajak)
                                ? ($d->pajak->nilai_pajak ?? 0)
                                : $d->pajak;

                            // hitung pajak nominal
                            $pajakNominal = ($d->total * $persen) / 100;

                            // hitung DPP
                            $dpp = $d->total - $pajakNominal;

                            // akumulasi
                            $totalDpp += $dpp;
                            $totalPajak += $pajakNominal;
                        @endphp

                        <tr>
                            <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d-m-Y') }}</td>

                            <td>{{ $d->kode_transaksi }}</td>

                            <td>
                                {{
                                    $d->pelangganRel->nama_pelanggan
                                    ?? $d->nama_pelanggan
                                    ?? 'Umum'
                                }}
                            </td>

                            <td>{{ $d->kategoriRel->nama_kategori ?? '-' }}</td>

                            <!-- DPP -->
                            <td class="text-primary">
                                Rp {{ number_format($dpp,0,',','.') }}
                            </td>

                            <!-- PAJAK -->
                            <td class="text-success">
                                Rp {{ number_format($pajakNominal,0,',','.') }}
                            </td>

                            <!-- TOTAL -->
                            <td class="fw-bold">
                                Rp {{ number_format($d->total,0,',','.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Tidak ada data penjualan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">TOTAL HARGA (DPP)</th>
                            <th class="text-primary">
                                Rp {{ number_format($totalDpp,0,',','.') }}
                            </th>
                            <th></th>
                            <th></th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">TOTAL PAJAK</th>
                            <th class="text-success">
                                Rp {{ number_format($totalPajak,0,',','.') }}
                            </th>
                            <th></th>
                        </tr>

                        <tr>
                            <th colspan="6" class="text-end">TOTAL KESELURUHAN</th>
                            <th class="fw-bold">
                                Rp {{ number_format($data->sum('total'),0,',','.') }}
                            </th>
                        </tr>
                    </tfoot>

                </table>

            </div>
        </div>

    </div>

</div>
@endsection


@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker"></script>

<script>
$(function () {
    $('#tanggal').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
        }
    });

    $('#tanggal').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(
            picker.startDate.format('YYYY-MM-DD') +
            ' - ' +
            picker.endDate.format('YYYY-MM-DD')
        );
    });
});
</script>
@endpush