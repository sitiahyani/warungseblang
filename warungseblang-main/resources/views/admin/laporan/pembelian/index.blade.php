@extends('layouts.admin')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>Laporan Pembelian</h4>
    </div>

    <div class="card-body">

        {{-- FILTER --}}
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <input type="date" name="tanggal_awal"
                       value="{{ request('tanggal_awal') }}"
                       class="form-control">
            </div>

            <div class="col-md-3">
                <input type="date" name="tanggal_akhir"
                       value="{{ request('tanggal_akhir') }}"
                       class="form-control">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>

            <div class="col-md-4 text-end">
                <a href="{{ route('laporan.pembelian.excel', request()->all()) }}"
                   class="btn btn-success">
                    Export Excel
                </a>

                <a href="{{ route('laporan.pembelian.pdf', request()->all()) }}"
                   class="btn btn-danger">
                    Cetak PDF
                </a>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Bahan</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>

            @foreach($pembelians as $pembelian)
                @foreach($pembelian->details as $detail)
                <tr>
                    <td>{{ $pembelian->tanggal }}</td>
                    <td>{{ $pembelian->supplier->nama_supplier }}</td>
                    <td>{{ $detail->bahan->nama_bahan }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ number_format($detail->harga,0,',','.') }}</td>
                    <td>{{ number_format($detail->subtotal,0,',','.') }}</td>
                </tr>
                @endforeach
            @endforeach

            </tbody>

            <tfoot>
                <tr>
                    <th colspan="5">TOTAL</th>
                    <th>{{ number_format($total,0,',','.') }}</th>
                </tr>
            </tfoot>
        </table>

    </div>
</div>

@endsection