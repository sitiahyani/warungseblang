@extends('layouts.admin')

@section('content')

<div class="card">

```
<div class="card-header">
    <h4>Laporan Laba Rugi</h4>
</div>

<div class="card-body">

    {{-- FILTER --}}
    <form method="GET"
          action="{{ route('admin.laba_rugi') }}"
          class="row g-2 mb-3 align-items-end">

        {{-- Tanggal Awal --}}
        <div class="col-md-3">
            <label class="form-label">Tanggal Awal</label>
            <input type="date"
                   name="tanggal_awal"
                   value="{{ request('tanggal_awal') }}"
                   class="form-control">
        </div>

        {{-- Tanggal Akhir --}}
        <div class="col-md-3">
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

            <a href="{{ route('admin.laba_rugi.pdf', request()->all()) }}"
               class="btn btn-danger">
                Cetak PDF
            </a>

        </div>

    </form>


    {{-- TABEL LAPORAN --}}
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

                {{-- PENDAPATAN --}}
                <tr>
                    <td colspan="3"><b>PENDAPATAN</b></td>
                </tr>

                <tr>
                    <td>Pendapatan Usaha</td>
                    <td>10</td>
                    <td>
                        Rp {{ number_format($pendapatan_usaha ?? 0,0,',','.') }}
                    </td>
                </tr>

                <tr>
                    <td>Pendapatan Lain-lain</td>
                    <td></td>
                    <td>
                        Rp {{ number_format($pendapatan_lain ?? 0,0,',','.') }}
                    </td>
                </tr>

                <tr class="table-secondary">
                    <th>JUMLAH PENDAPATAN</th>
                    <th></th>
                    <th>
                        Rp {{ number_format($total_pendapatan ?? 0,0,',','.') }}
                    </th>
                </tr>


                {{-- BEBAN --}}
                <tr>
                    <td colspan="3"><b>BEBAN</b></td>
                </tr>

                <tr>
                    <td>Beban Usaha</td>
                    <td></td>
                    <td>
                        Rp {{ number_format($beban_usaha ?? 0,0,',','.') }}
                    </td>
                </tr>

                <tr>
                    <td>Beban Lain-lain</td>
                    <td>11</td>
                    <td>
                        Rp {{ number_format($beban_lain ?? 0,0,',','.') }}
                    </td>
                </tr>

                <tr class="table-secondary">
                    <th>JUMLAH BEBAN</th>
                    <th></th>
                    <th>
                        Rp {{ number_format($total_beban ?? 0,0,',','.') }}
                    </th>
                </tr>


                {{-- LABA --}}
                <tr class="table-success">
                    <th>LABA (RUGI) SEBELUM PAJAK</th>
                    <th></th>
                    <th>
                        Rp {{ number_format($laba_sebelum_pajak ?? 0,0,',','.') }}
                    </th>
                </tr>

                <tr>
                    <td>Beban Pajak</td>
                    <td>12</td>
                    <td>
                        Rp {{ number_format($pajak ?? 0,0,',','.') }}
                    </td>
                </tr>

            </tbody>

            <tfoot>
                <tr class="table-primary">
                    <th colspan="2">LABA SETELAH PAJAK</th>
                    <th>
                        Rp {{ number_format($laba_setelah_pajak ?? 0,0,',','.') }}
                    </th>
                </tr>
            </tfoot>

        </table>

    </div>

</div>
```

</div>

@endsection
