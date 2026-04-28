<h2 style="text-align:center;">BUKU BESAR</h2>

<table border="1" width="100%" cellspacing="0" cellpadding="5">

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

@foreach($details as $d)

<tr>

<td>{{ $d->jurnal->tanggal }}</td>

<td>{{ $d->jurnal->keterangan }}</td>

<td>{{ $d->akun->nama_akun }}</td>

<td align="right">
{{ number_format($d->debit,0,',','.') }}
</td>

<td align="right">
{{ number_format($d->kredit,0,',','.') }}
</td>

<td align="right">
{{ number_format($d->saldo,0,',','.') }}
</td>

</tr>

@endforeach

</tbody>

</table>