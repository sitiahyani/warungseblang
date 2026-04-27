@extends('layouts.admin')

@section('title', 'Laporan Shift')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h4 class="mb-0">📊 Laporan Shift Kasir</h4>
        </div>

        <!-- ACTION BUTTON -->
        <div class="d-flex justify-content-end mb-3 px-3">
            <a href="{{ route('laporan.shift.pdf', request()->all()) }}" class="btn btn-danger me-2">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>

            <a href="{{ route('laporan.shift.excel', request()->all()) }}" class="btn btn-success">
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
                                placeholder="2026-04-01 - 2026-04-30">
                        </div>

                        <!-- SHIFT -->
                        <div class="col-md-3">
                            <label>Nama Shift</label>
                            <select name="nama_shift" class="form-control">
                                <option value="">Semua</option>
                                @foreach($shiftList as $shift)
                                    <option value="{{ $shift->id_shift }}"
                                        {{ request('nama_shift') == $shift->id_shift ? 'selected' : '' }}>
                                        {{ $shift->nama_shift }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- KASIR -->
                        <div class="col-md-3">
                            <label>Kasir</label>
                            <select name="kasir" class="form-control">
                                <option value="">Semua</option>
                                @foreach($kasirList as $kasir)
                                    <option value="{{ $kasir->id_user }}"
                                        {{ request('kasir') == $kasir->id_user ? 'selected' : '' }}>
                                        {{ $kasir->nama ?? $kasir->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- BUTTON -->
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100 me-2">
                                Terapkan
                            </button>

                            <a href="{{ route('laporan.shift') }}" class="btn btn-secondary w-100">
                                Reset
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Nama Shift</th>
                            <th>Kasir</th>
                            <th>Waktu Shift</th>
                            <th>Cash Awal</th>
                            <th>Cash Akhir</th>
                            <th>Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $item)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                </td>
                                <td>{{ $item->shift->kode ?? '-' }}</td>
                                <td>{{ $item->shift->nama_shift ?? '-' }}</td>
                                <td>
                                    {{ $item->user->nama
                                        ?? $item->user->username
                                        ?? $item->user->name
                                        ?? 'Kasir' }}
                                </td>
                                <td>
                                    {{ $item->shift->waktu_mulai ?? '-' }}
                                    -
                                    {{ $item->shift->waktu_selesai ?? '-' }}
                                </td>
                                <td>
                                    Rp {{ number_format($item->cash_awal ?? 0,0,',','.') }}
                                </td>
                                <td>
                                    Rp {{ number_format($item->cash_akhir ?? 0,0,',','.') }}
                                </td>
                                <td>
                                    <strong class="{{ ($item->selisih ?? 0) == 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($item->selisih ?? 0,0,',','.') }}
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Belum ada data laporan shift
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