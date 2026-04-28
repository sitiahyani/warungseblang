@extends('layouts.admin')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>Laporan Hutang</h4>
    </div>

    <div class="card-body">

        {{-- FILTER --}}
        <form method="GET"
              action="{{ route('laporan.hutang') }}"
              class="row g-2 mb-3 align-items-end">

            {{-- Status --}}
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">-- Semua --</option>
                    <option value="belum"
                        {{ request('status') == 'belum' ? 'selected' : '' }}>
                        Belum Lunas
                    </option>
                    <option value="lunas"
                        {{ request('status') == 'lunas' ? 'selected' : '' }}>
                        Lunas
                    </option>
                </select>
            </div>

            {{-- Tanggal Awal --}}
            <div class="col-md-2">
                <label class="form-label">Tanggal Awal</label>
                <input type="date"
                       name="tanggal_awal"
                       value="{{ request('tanggal_awal') }}"
                       class="form-control">
            </div>

            {{-- Tanggal Akhir --}}
            <div class="col-md-2">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date"
                       name="tanggal_akhir"
                       value="{{ request('tanggal_akhir') }}"
                       class="form-control">
            </div>

            {{-- Tombol Filter --}}
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    Filter
                </button>
            </div>

            {{-- Tombol Export --}}
            <div class="col-md-4 text-end">
                <a href="{{ route('laporan.hutang.excel', request()->all()) }}"
                   class="btn btn-success">
                    Export Excel
                </a>

                <a href="{{ route('laporan.hutang.pdf', request()->all()) }}"
                   class="btn btn-danger">
                    Cetak PDF
                </a>
            </div>

        </form>

        {{-- TABEL --}}
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Supplier</th>
                        <th>Total Hutang</th>
                        <th>Sisa</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($hutangs as $hutang)
                        <tr>
                            <td>{{ $hutang->supplier->nama_supplier ?? '-' }}</td>
                            <td>
                                Rp {{ number_format($hutang->total,0,',','.') }}
                            </td>
                            <td>
                                Rp {{ number_format($hutang->sisa,0,',','.') }}
                            </td>
                            <td>
                                <span class="badge 
                                    {{ $hutang->status == 'lunas'
                                        ? 'bg-success'
                                        : 'bg-danger' }}">
                                    {{ strtoupper($hutang->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="2">TOTAL SISA HUTANG</th>
                        <th colspan="2">
                            Rp {{ number_format($totalSisa,0,',','.') }}
                        </th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>
</div>

@endsection