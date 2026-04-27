@extends('layouts.admin')

@section('content')
<div class="card shadow">

    <div class="card-header">
        <h4>Catatan Atas Laporan Keuangan</h4>
    </div>

    <div class="card-body">

        {{-- FILTER --}}
        <form method="GET"
              action="{{ route('calk') }}"
              class="row g-2 mb-3 align-items-end">

            <div class="col-md-3">
                <label class="form-label">Tanggal</label>
                <input type="date"
                       name="tanggal"
                       value="{{ request('tanggal') }}"
                       class="form-control">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    Filter
                </button>
            </div>

            <div class="col-md-7 text-end">
                <a href="{{ route('calk.pdf', request()->all()) }}"
                   class="btn btn-danger">
                    Cetak PDF
                </a>
            </div>
        </form>


        {{-- TABEL CALK --}}
        <div class="table-responsive">
            <table class="table table-bordered">

                <thead class="table-light">
                    <tr>
                        <th width="60%">Keterangan</th>
                        <th width="20%">Catatan</th>
                        <th width="20%">Jumlah</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td colspan="3"><b>UMUM</b></td>
                    </tr>
                    <tr>
                        <td>Warung Seblang bergerak di bidang restoran dan penjualan makanan minuman.</td>
                        <td>1</td>
                        <td>-</td>
                    </tr>

                    <tr>
                        <td colspan="3"><b>ASET</b></td>
                    </tr>
                    <tr>
                        <td>Kas dan Setara Kas</td>
                        <td>3</td>
                        <td>Rp {{ number_format($kas ?? 0,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>Piutang Usaha</td>
                        <td>6</td>
                        <td>Rp {{ number_format($piutang ?? 0,0,',','.') }}</td>
                    </tr>

                    <tr>
                        <td colspan="3"><b>LIABILITAS</b></td>
                    </tr>
                    <tr>
                        <td>Utang Bank</td>
                        <td>8</td>
                        <td>Rp {{ number_format($utang ?? 0,0,',','.') }}</td>
                    </tr>

                    <tr>
                        <td colspan="3"><b>EKUITAS</b></td>
                    </tr>
                    <tr>
                        <td>Modal</td>
                        <td>9</td>
                        <td>Rp {{ number_format($modal ?? 0,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>Saldo Laba</td>
                        <td>9</td>
                        <td>Rp {{ number_format($laba ?? 0,0,',','.') }}</td>
                    </tr>

                    <tr>
                        <td colspan="3"><b>LABA RUGI</b></td>
                    </tr>
                    <tr>
                        <td>Pendapatan Penjualan</td>
                        <td>10</td>
                        <td>Rp {{ number_format($pendapatan ?? 0,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>Beban Lain-lain</td>
                        <td>11</td>
                        <td>Rp {{ number_format($beban ?? 0,0,',','.') }}</td>
                    </tr>

                </tbody>

            </table>
        </div>

    </div>
</div>
@endsection