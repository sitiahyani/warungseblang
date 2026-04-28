<!DOCTYPE html>
<html>
<head>
    <title>Laporan Piutang</title>
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
    LAPORAN PIUTANG PELANGGAN
</div>

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Invoice</th>
            <th>Total</th>
            <th>Terbayar</th>
            <th>Sisa</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $d)
        <tr>
            <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d-m-Y') }}</td>
            <td>{{ $d->kode_transaksi }}</td>
            <td class="text-right">{{ number_format($d->total,0,',','.') }}</td>
            <td class="text-right">{{ number_format($d->terbayar,0,',','.') }}</td>
            <td class="text-right">{{ number_format($d->sisa,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4">TOTAL SISA PIUTANG</th>
            <th class="text-right">
                {{ number_format($data->sum('sisa'),0,',','.') }}
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