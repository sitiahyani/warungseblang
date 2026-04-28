@extends('layouts.admin')

@section('title', 'Laporan Piutang')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">

        <!-- HEADER -->
        <div class="card-header bg-white border-0 py-3">
            <h4 class="mb-0">📊 Laporan Piutang (Pembayaran Hutang)</h4>
        </div>

        <!-- ACTION -->
        <div class="d-flex justify-content-end mb-3 px-3">
            <a href="{{ route('laporan.piutang.pdf', request()->all()) }}" class="btn btn-danger me-2">
                Cetak PDF
            </a>
            <a href="{{ route('laporan.piutang.excel', request()->all()) }}" class="btn btn-success">
                Export Excel
            </a>
        </div>

        <!-- FILTER -->
        <form method="GET" class="px-3">
            <div class="card mb-3 p-3">
                <div class="row">

                    <!-- TANGGAL -->
                    <div class="col-md-3">
                        <label>Tanggal</label>
                        <input type="text" name="tanggal" id="tanggal"
                            class="form-control"
                            value="{{ request('tanggal') }}"
                            placeholder="YYYY-MM-DD - YYYY-MM-DD">
                    </div>

                    <!-- SEARCH (KODE + PELANGGAN) -->
                    <div class="col-md-3">
                        <label>Cari (Kode / Pelanggan)</label>
                        <input type="text" name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="WD-2026 / nama pelanggan">
                    </div>

                    <!-- STATUS -->
                    <div class="col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">-- Semua --</option>
                            <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>
                                Lunas
                            </option>
                            <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>
                                Belum
                            </option>
                        </select>
                    </div>

                    <!-- BUTTON -->
                    <div class="col-md-3 d-flex align-items-end">
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

                    <thead class="table-light text-center">
                        <tr>
                            <th>Tanggal</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Terbayar</th>
                            <th>Sisa</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $d)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d-m-Y') }}</td>

                            <td>{{ $d->kode_transaksi }}</td>

                            <td>{{ $d->pelanggan }}</td>

                            <td class="text-primary fw-bold">
                                Rp {{ number_format($d->total,0,',','.') }}
                            </td>

                            <td class="text-success fw-bold">
                                Rp {{ number_format($d->terbayar,0,',','.') }}
                            </td>

                            <td class="text-danger fw-bold">
                                Rp {{ number_format($d->sisa,0,',','.') }}
                            </td>

                            <td class="text-center">
                                @if($d->status == 'Lunas')
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Tidak ada data
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">TOTAL</th>
                            <th>Rp {{ number_format($data->sum('total'),0,',','.') }}</th>
                            <th>Rp {{ number_format($data->sum('total_bayar'),0,',','.') }}</th>
                            <th>Rp {{ number_format($data->sum('sisa'),0,',','.') }}</th>
                            <th></th>
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

    $('#tanggal').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});
</script>
@endpush