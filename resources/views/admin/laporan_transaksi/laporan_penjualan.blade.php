@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">
        
        <!-- HEADER -->
        <div class="card-header bg-white border-0 py-3">
            <h4 class="mb-0">📊 Laporan Penjualan</h4>
        </div>

        <!-- ACTION BUTTON -->
        <div class="d-flex justify-content-end mb-3 px-3">
            <a href="{{ route('laporan.penjualan.pdf', request()->all()) }}" 
               class="btn btn-danger me-2">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>

            <a href="{{ route('laporan.penjualan.excel', request()->all()) }}" 
               class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>

        <!-- FILTER -->
        <form method="GET">
            <div class="px-3 mb-3">
                <div class="card p-3">
                    <div class="row">

                        <!-- TANGGAL -->
                        <div class="col-md-3">
                            <label>Rentang Tanggal</label>
                            <input type="text"
                                   name="tanggal"
                                   id="tanggal"
                                   class="form-control"
                                   value="{{ request('tanggal') }}"
                                   placeholder="2026-03-01 - 2026-03-31">
                        </div>

                        <!-- KATEGORI -->
                        <div class="col-md-3">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control">
                                <option value="">Semua</option>
                                @foreach($kategori ?? [] as $k)
                                    <option value="{{ $k->id_kategori }}"
                                        {{ request('kategori') == $k->id_kategori ? 'selected' : '' }}>
                                        {{ $k->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- METODE BAYAR -->
                        <div class="col-md-3">
                            <label>Metode Pembayaran</label>
                            <select name="metode_bayar" class="form-control">
                                <option value="">Semua</option>
                                <option value="tunai" {{ request('metode_bayar')=='tunai'?'selected':'' }}>
                                    Tunai
                                </option>
                                <option value="kredit" {{ request('metode_bayar')=='kredit'?'selected':'' }}>
                                    Kredit
                                </option>
                            </select>
                        </div>

                        <!-- BUTTON -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100">
                                Terapkan
                            </button>
                        </div>

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
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($penjualan as $p)
                            <tr>

                                <!-- TANGGAL -->
                                <td>
                                    {{ \Carbon\Carbon::parse($p->tanggal)->format('d-m-Y H:i') }}
                                </td>

                                <!-- INVOICE -->
                                <td>{{ $p->kode_transaksi ?? '-' }}</td>

                                <!-- PELANGGAN -->
                                <td>
                                    {{ $p->pelangganRel->nama_pelanggan 
                                        ?? $p->nama_pelanggan 
                                        ?? '-' }}
                                </td>

                                <!-- KATEGORI -->
                                <td>
                                    {{ $p->kategoriRel->nama_kategori ?? '-' }}
                                </td>

                                <!-- ITEM -->
                                <td>
                                    {{ 
                                        ($p->details->count() ?? 0) + 
                                        ($p->detailLayanan->count() ?? 0) 
                                    }} item
                                </td>

                                <!-- TOTAL -->
                                <td class="text-success">
                                    Rp {{ number_format($p->total ?? 0,0,',','.') }}
                                </td>

                                <!-- STATUS -->
                                <td>
                                    @if(($p->status ?? '') == 'lunas')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-warning text-dark">DP</span>
                                    @endif
                                </td>

                                <!-- METODE -->
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($p->metode_bayar ?? '-') }}
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Belum ada data laporan penjualan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

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