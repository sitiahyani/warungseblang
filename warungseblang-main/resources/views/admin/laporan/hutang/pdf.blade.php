<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hutang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

<h3 style="text-align:center;">LAPORAN HUTANG</h3>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Supplier</th>
            <th>Total Hutang</th>
            <th>Sisa Hutang</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($hutangs as $index => $hutang)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $hutang->tanggal }}</td>
            <td>{{ $hutang->supplier->nama_supplier ?? '-' }}</td>
            <td class="text-right">Rp {{ number_format($hutang->total_hutang,0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($hutang->sisa_hutang,0,',','.') }}</td>
            <td>{{ $hutang->keterangan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br>

<table>
    <tr>
        <td><strong>Total Hutang</strong></td>
        <td class="text-right"><strong>Rp {{ number_format($total_hutang,0,',','.') }}</strong></td>
    </tr>
    <tr>
        <td><strong>Total Sisa Hutang</strong></td>
        <td class="text-right"><strong>Rp {{ number_format($total_sisa,0,',','.') }}</strong></td>
    </tr>
</table>

</body>
</html>