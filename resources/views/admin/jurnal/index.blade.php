@extends('layouts.admin')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>Jurnal Umum</h4>
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
                <a href="{{ route('jurnal.excel', request()->all()) }}"
                   class="btn btn-success">
                    Export Excel
                </a>

                <a href="{{ route('jurnal.pdf', request()->all()) }}"
                   class="btn btn-danger">
                    Export PDF
                </a>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Debit</th>
                    <th>Kredit</th>
                </tr>
            </thead>
            <tbody>

            @foreach($jurnals as $jurnal)
                @foreach($jurnal->details as $detail)
                <tr>
                    <td>{{ $jurnal->tanggal }}</td>
                    <td>{{ $jurnal->keterangan }}</td>
                    <td class="text-end">
                        {{ number_format($detail->debit,0,',','.') }}
                    </td>
                    <td class="text-end">
                        {{ number_format($detail->kredit,0,',','.') }}
                    </td>
                </tr>
                @endforeach
            @endforeach

            </tbody>

            <tfoot>
                <tr>
                    <th colspan="2">TOTAL</th>
                    <th class="text-end">
                        {{ number_format($totalDebit,0,',','.') }}
                    </th>
                    <th class="text-end">
                        {{ number_format($totalKredit,0,',','.') }}
                    </th>
                </tr>
            </tfoot>
        </table>

    </div>
</div>

@endsection
