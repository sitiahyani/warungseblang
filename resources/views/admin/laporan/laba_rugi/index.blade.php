@extends('layouts.admin')

@section('content')

@php
function rupiah($n){
    if ($n < 0) return '(' . number_format(abs($n),0,',','.') . ')';
    return number_format($n,0,',','.');
}
@endphp

<div class="card">

<div class="card-header text-center">
    <h5 class="mb-0"><b>LAPORAN LABA RUGI</b></h5>
    <small>Untuk Tahun yang Berakhir {{ $tahun ?? date('Y') }} dan {{ $tahun_lalu ?? date('Y')-1 }}</small>
</div>

<div class="card-body">

{{-- FILTER --}}
<form method="GET"
      action="{{ route('admin.laba_rugi') }}"
      class="row g-2 mb-3 align-items-end">

    <div class="col-md-3">
        <label class="form-label">Tanggal Awal</label>
        <input type="date"
               name="tanggal_awal"
               value="{{ request('tanggal_awal') }}"
               class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Tanggal Akhir</label>
        <input type="date"
               name="tanggal_akhir"
               value="{{ request('tanggal_akhir') }}"
               class="form-control">
    </div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">
            Filter
        </button>
    </div>

    <div class="col-md-4 text-end">
        <a href="{{ route('admin.laba_rugi.pdf', request()->all()) }}"
           class="btn btn-danger">
            Cetak PDF
        </a>
    </div>

</form>


<div class="table-responsive">

<table class="table table-bordered">

<thead class="table-light">
<tr>
    <th width="55%">Keterangan</th>
    <th width="15%">Catatan</th>
    <th width="15%" class="text-end">{{ $tahun ?? date('Y') }}</th>
    <th width="15%" class="text-end">{{ $tahun_lalu ?? date('Y')-1 }}</th>
</tr>
</thead>

<tbody>

{{-- ================= PENDAPATAN ================= --}}
<tr>
    <td colspan="4"><b>PENDAPATAN</b></td>
</tr>

<tr>
    <td>Pendapatan usaha</td>
    <td>10</td>
    <td class="text-end">{{ rupiah($pendapatan_usaha ?? 0) }}</td>
    <td class="text-end">{{ rupiah($pendapatan_usaha_lalu ?? 0) }}</td>
</tr>

<tr>
    <td>Pendapatan lain-lain</td>
    <td></td>
    <td class="text-end">{{ rupiah($pendapatan_lain ?? 0) }}</td>
    <td class="text-end">{{ rupiah($pendapatan_lain_lalu ?? 0) }}</td>
</tr>

<tr style="border-top:2px solid black;">
    <td><b>JUMLAH PENDAPATAN</b></td>
    <td></td>
    <td class="text-end"><b>{{ rupiah($total_pendapatan ?? 0) }}</b></td>
    <td class="text-end"><b>{{ rupiah($total_pendapatan_lalu ?? 0) }}</b></td>
</tr>


{{-- ================= BEBAN ================= --}}
<tr>
    <td colspan="4"><b>BEBAN</b></td>
</tr>

<tr>
    <td>Beban usaha</td>
    <td></td>
    <td class="text-end">{{ rupiah($beban_usaha ?? 0) }}</td>
    <td class="text-end">{{ rupiah($beban_usaha_lalu ?? 0) }}</td>
</tr>

<tr>
    <td>Beban lain-lain</td>
    <td>11</td>
    <td class="text-end">{{ rupiah($beban_lain ?? 0) }}</td>
    <td class="text-end">{{ rupiah($beban_lain_lalu ?? 0) }}</td>
</tr>

<tr style="border-top:2px solid black;">
    <td><b>JUMLAH BEBAN</b></td>
    <td></td>
    <td class="text-end"><b>{{ rupiah($total_beban ?? 0) }}</b></td>
    <td class="text-end"><b>{{ rupiah($total_beban_lalu ?? 0) }}</b></td>
</tr>


{{-- ================= LABA ================= --}}
<tr>
    <td><b>LABA (RUGI) SEBELUM PAJAK PENGHASILAN</b></td>
    <td></td>
    <td class="text-end">{{ rupiah($laba_sebelum_pajak ?? 0) }}</td>
    <td class="text-end">{{ rupiah($laba_sebelum_pajak_lalu ?? 0) }}</td>
</tr>

<tr>
    <td>Beban pajak penghasilan</td>
    <td>12</td>
    <td class="text-end">{{ rupiah($pajak ?? 0) }}</td>
    <td class="text-end">{{ rupiah($pajak_lalu ?? 0) }}</td>
</tr>

<tr style="border-top:2px solid black;">
    <td><b>LABA (RUGI) SETELAH PAJAK PENGHASILAN</b></td>
    <td></td>
    <td class="text-end"><b>{{ rupiah($laba_setelah_pajak ?? 0) }}</b></td>
    <td class="text-end"><b>{{ rupiah($laba_setelah_pajak_lalu ?? 0) }}</b></td>
</tr>

</tbody>

</table>

</div>

</div>
</div>

@endsection