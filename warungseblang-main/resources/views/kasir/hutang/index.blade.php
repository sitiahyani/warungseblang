@extends('layouts.kasir')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>Pembayaran Hutang</h4>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Total</th>
                    <th>Sisa</th>
                    <th>Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hutangs as $hutang)
                <tr>
                    <td>{{ $hutang->supplier->nama_supplier }}</td>
                    <td>{{ number_format($hutang->total,0,',','.') }}</td>
                    <td>{{ number_format($hutang->sisa,0,',','.') }}</td>
                    <td>
                        <form method="POST"
                              action="{{ route('kasir.hutang.bayar') }}">
                            @csrf
                            <input type="hidden"
                                   name="id_hutang"
                                   value="{{ $hutang->id_hutang }}">

                            <input type="number"
                                   name="jumlah_bayar"
                                   class="form-control mb-2"
                                   placeholder="Jumlah bayar"
                                   required>

                            <button class="btn btn-primary btn-sm">
                                Bayar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection