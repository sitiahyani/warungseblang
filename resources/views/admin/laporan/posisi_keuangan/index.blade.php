@extends('layouts.admin')

@section('content')

@php
function rupiah($n){
    if ($n < 0) return '(' . number_format(abs($n),0,',','.') . ')';
    return number_format($n,0,',','.');
}
@endphp

<div class="card">

<div class="card-header">
<h4 class="text-center mb-0"><b>Laporan Posisi Keuangan</b></h4>
<p class="text-center mb-0">31 Desember {{ $tahun }} dan {{ $tahun_lalu }}</p>
</div>

<div class="card-body">

{{-- FILTER --}}
<form method="GET"
      action="{{ route('admin.posisi_keuangan') }}"
      class="row g-2 mb-3 align-items-end">

    <div class="col-md-3">
        <label class="form-label">Tahun</label>
        <input type="number"
               name="tahun"
               value="{{ request('tahun') ?? date('Y') }}"
               class="form-control">
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100">Filter</button>
    </div>

    <div class="col-md-7 text-end">
        <a href="{{ route('admin.posisi_keuangan.pdf', request()->all()) }}"
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
    <th width="15%" class="text-end">{{ $tahun }}</th>
    <th width="15%" class="text-end">{{ $tahun_lalu }}</th>
</tr>
</thead>

<tbody>

{{-- ================= ASET ================= --}}
<tr>
    <td colspan="4"><b>ASET</b></td>
</tr>

<tr>
    <td>Kas dan setara kas</td>
    <td></td><td></td><td></td>
</tr>

<tr>
    <td style="padding-left:25px;">Kas</td>
    <td>3</td>
    <td class="text-end">{{ rupiah($kas) }}</td>
    <td class="text-end">{{ rupiah($kas_lalu) }}</td>
</tr>

<tr>
    <td style="padding-left:25px;">Giro</td>
    <td>4</td>
    <td class="text-end">{{ rupiah($giro) }}</td>
    <td class="text-end">{{ rupiah($giro_lalu) }}</td>
</tr>

<tr>
    <td style="padding-left:25px;">Deposito</td>
    <td>5</td>
    <td class="text-end">{{ rupiah($deposito) }}</td>
    <td class="text-end">{{ rupiah($deposito_lalu) }}</td>
</tr>

<tr>
    <td style="padding-left:25px;"><b>Jumlah kas dan setara kas</b></td>
    <td></td>
    <td class="text-end"><b>{{ rupiah($kas+$giro+$deposito) }}</b></td>
    <td class="text-end"><b>{{ rupiah($kas_lalu+$giro_lalu+$deposito_lalu) }}</b></td>
</tr>

<tr>
    <td>Piutang usaha</td>
    <td>6</td>
    <td class="text-end">{{ rupiah($piutang) }}</td>
    <td class="text-end">{{ rupiah($piutang_lalu) }}</td>
</tr>

<tr>
    <td>Persediaan</td>
    <td></td>
    <td class="text-end">{{ rupiah($persediaan) }}</td>
    <td class="text-end">{{ rupiah($persediaan_lalu) }}</td>
</tr>

<tr>
    <td>Beban dibayar di muka</td>
    <td>7</td>
    <td class="text-end">{{ rupiah($beban_dimuka) }}</td>
    <td class="text-end">{{ rupiah($beban_dimuka_lalu) }}</td>
</tr>

<tr>
    <td>Aset tetap</td>
    <td></td>
    <td class="text-end">{{ rupiah($aset_tetap) }}</td>
    <td class="text-end">{{ rupiah($aset_tetap_lalu) }}</td>
</tr>

<tr>
    <td style="padding-left:25px;">Akumulasi penyusutan</td>
    <td></td>
    <td class="text-end">{{ rupiah($akumulasi_penyusutan) }}</td>
    <td class="text-end">{{ rupiah($akumulasi_penyusutan_lalu) }}</td>
</tr>

<tr style="border-top:2px solid black;">
    <td><b>JUMLAH ASET</b></td>
    <td></td>
    <td class="text-end">
        <b>{{ rupiah($kas+$giro+$deposito+$piutang+$persediaan+$beban_dimuka+$aset_tetap+$akumulasi_penyusutan) }}</b>
    </td>
    <td class="text-end">
        <b>{{ rupiah($kas_lalu+$giro_lalu+$deposito_lalu+$piutang_lalu+$persediaan_lalu+$beban_dimuka_lalu+$aset_tetap_lalu+$akumulasi_penyusutan_lalu) }}</b>
    </td>
</tr>


{{-- ================= LIABILITAS ================= --}}
<tr>
    <td colspan="4"><b>LIABILITAS</b></td>
</tr>

<tr>
    <td>Utang usaha</td>
    <td></td>
    <td class="text-end">{{ rupiah($utang_usaha) }}</td>
    <td class="text-end">{{ rupiah($utang_usaha_lalu) }}</td>
</tr>

<tr>
    <td>Utang bank</td>
    <td>8</td>
    <td class="text-end">{{ rupiah($utang_bank) }}</td>
    <td class="text-end">{{ rupiah($utang_bank_lalu) }}</td>
</tr>

<tr style="border-top:2px solid black;">
    <td><b>JUMLAH LIABILITAS</b></td>
    <td></td>
    <td class="text-end"><b>{{ rupiah($utang_usaha+$utang_bank) }}</b></td>
    <td class="text-end"><b>{{ rupiah($utang_usaha_lalu+$utang_bank_lalu) }}</b></td>
</tr>


{{-- ================= EKUITAS ================= --}}
<tr>
    <td colspan="4"><b>EKUITAS</b></td>
</tr>

<tr>
    <td>Modal</td>
    <td></td>
    <td class="text-end">{{ rupiah($modal) }}</td>
    <td class="text-end">{{ rupiah($modal_lalu) }}</td>
</tr>

<tr>
    <td>Saldo laba (defisit)</td>
    <td>9</td>
    <td class="text-end">{{ rupiah($laba) }}</td>
    <td class="text-end">{{ rupiah($laba_lalu) }}</td>
</tr>

<tr style="border-top:2px solid black;">
    <td><b>JUMLAH EKUITAS</b></td>
    <td></td>
    <td class="text-end"><b>{{ rupiah($modal+$laba) }}</b></td>
    <td class="text-end"><b>{{ rupiah($modal_lalu+$laba_lalu) }}</b></td>
</tr>

<tr style="border-top:3px double black;">
    <td><b>JUMLAH LIABILITAS DAN EKUITAS</b></td>
    <td></td>
    <td class="text-end"><b>{{ rupiah($utang_usaha+$utang_bank+$modal+$laba) }}</b></td>
    <td class="text-end"><b>{{ rupiah($utang_usaha_lalu+$utang_bank_lalu+$modal_lalu+$laba_lalu) }}</b></td>
</tr>

</tbody>

</table>

</div>

</div>
</div>

@endsection