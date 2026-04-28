@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header">
        <h4>Buku Besar</h4>
    </div>

    <div class="card-body">

        {{-- FILTER --}}
        <form method="GET" class="row mb-3">

            <div class="col-md-3">
                <input type="date"
                       name="tanggal_awal"
                       value="{{ request('tanggal_awal') }}"
                       class="form-control">
            </div>

            <div class="col-md-3">
                <input type="date"
                       name="tanggal_akhir"
                       value="{{ request('tanggal_akhir') }}"
                       class="form-control">
            </div>

            <div class="col-md-3">
                <select name="akun"
                        class="form-control">
                    <option value="">Semua Akun</option>

                    @foreach($akuns as $akun)

                        <option value="{{ $akun->id_akun }}"
                        {{ request('akun') == $akun->id_akun ? 'selected' : '' }}>

                        {{ $akun->nama_akun }}

                        </option>

                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-primary">
                    Filter
                </button>
            </div>

        </form>


        <div class="mb-3 text-end">

            <a href="{{ route('bukubesar.excel', request()->all()) }}"
               class="btn btn-success">

                Export Excel

            </a>

            <a href="{{ route('bukubesar.pdf', request()->all()) }}"
               class="btn btn-danger">

                Cetak PDF

            </a>

        </div>


        <table class="table table-bordered">

            <thead>

                <tr>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Akun</th>
                    <th>Debit</th>
                    <th>Kredit</th>
                    <th>Saldo</th>
                </tr>

            </thead>

            <tbody>

            @php
                $saldo = 0;
            @endphp

            @foreach($details as $detail)

            @php
                $saldo += $detail->debit - $detail->kredit;
            @endphp

            <tr>

                <td>
                    {{ $detail->jurnal->tanggal }}
                </td>

                <td>
                    {{ $detail->jurnal->keterangan }}
                </td>

                <td>
                    {{ $detail->akun->nama_akun }}
                </td>

                <td class="text-end">

                    {{ number_format($detail->debit,0,',','.') }}

                </td>

                <td class="text-end">

                    {{ number_format($detail->kredit,0,',','.') }}

                </td>

                <td class="text-end">

                    {{ number_format($saldo,0,',','.') }}

                </td>

            </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection