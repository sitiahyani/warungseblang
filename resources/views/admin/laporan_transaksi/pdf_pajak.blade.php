<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pajak</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }

        .kop {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .judul {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }

        table {
            width:100%;
            border-collapse: collapse;
        }

        th, td {
            border:1px solid #000;
            padding:6px;
        }

        th { background:#f2f2f2; }
        .text-right { text-align:right; }
    </style>
</head>
<body>

<div class="kop">
    <h2>WARUNG SEBLANG</h2>
    <p>Singojuruh, Banyuwangi</p>
    <p>Telp: 0812-3397-1802</p>
</div>

<div class="judul">
    LAPORAN PAJAK ({{ $persen }}%)
</div>

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Invoice</th>
            <th>Total</th>
            <th>Pajak</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $d)
        <tr>
            <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d-m-Y') }}</td>
            <td>{{ $d->kode_transaksi }}</td>
            <td class="text-right">{{ number_format($d->total,0,',','.') }}</td>
            <td class="text-right">{{ number_format($d->nilai_pajak,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="3">TOTAL PAJAK</th>
            <th class="text-right">
                {{ number_format($data->sum('nilai_pajak'),0,',','.') }}
            </th>
        </tr>
    </tfoot>
</table>

<br><br>

<div style="text-align:right;">
    Banyuwangi, {{ date('d M Y') }}
</div>

</body>
</html>