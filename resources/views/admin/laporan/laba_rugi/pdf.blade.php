@extends('layouts.admin')

@section('content')

<div class="card">
<div class="card-body">

<center>
<h4><b>WARUNG SEBLANG</b></h4>
<h5>LAPORAN LABA RUGI</h5>
<p>Untuk Tahun yang Berakhir 31 Desember {{ date('Y') }}</p>
</center>

<table class="table table-bordered">

<thead>
<tr>
<th width="60%">Keterangan</th>
<th>Catatan</th>
<th>20{{ date('y') }}</th>
</tr>
</thead>

<tbody>

<tr>
<td colspan="3"><b>PENDAPATAN</b></td>
</tr>

<tr>
<td>Pendapatan usaha</td>
<td>10</td>
<td>Rp {{ number_format($pendapatan_usaha,0,',','.') }}</td>
</tr>

<tr>
<td>Pendapatan lain-lain</td>
<td></td>
<td>Rp {{ number_format($pendapatan_lain,0,',','.') }}</td>
</tr>

<tr>
<td><b>JUMLAH PENDAPATAN</b></td>
<td></td>
<td><b>Rp {{ number_format($total_pendapatan,0,',','.') }}</b></td>
</tr>


<tr>
<td colspan="3"><b>BEBAN</b></td>
</tr>

<tr>
<td>Beban usaha</td>
<td></td>
<td>Rp {{ number_format($beban_usaha,0,',','.') }}</td>
</tr>

<tr>
<td>Beban lain-lain</td>
<td>11</td>
<td>Rp {{ number_format($beban_lain,0,',','.') }}</td>
</tr>

<tr>
<td><b>JUMLAH BEBAN</b></td>
<td></td>
<td><b>Rp {{ number_format($total_beban,0,',','.') }}</b></td>
</tr>


<tr>
<td><b>LABA (RUGI) SEBELUM PAJAK</b></td>
<td></td>
<td><b>Rp {{ number_format($laba_sebelum_pajak,0,',','.') }}</b></td>
</tr>

<tr>
<td>Beban pajak penghasilan</td>
<td>12</td>
<td>Rp {{ number_format($pajak,0,',','.') }}</td>
</tr>


<tr>
<td><b>LABA (RUGI) SETELAH PAJAK</b></td>
<td></td>
<td><b>Rp {{ number_format($laba_setelah_pajak,0,',','.') }}</b></td>
</tr>

</tbody>

</table>

</div>
</div>

@endsection