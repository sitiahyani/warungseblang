<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }

        .kop {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop h2 { margin: 0; }
        .kop p { margin: 2px 0; font-size: 12px; }

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
            font-size:12px;
        }

        th { background-color: #f2f2f2; }

        .text-right { text-align:right; }
    </style>
</head>
<body>

<!-- 🔥 KOP -->
<div class="kop">
    <h2>WARUNG SEBLANG</h2>
    <p>Jl Krajan Timur Desa, Singojuruh, Banyuwangi</p>
    <p>Telp: 0812-3397-1802</p>
</div>

<!-- JUDUL -->
<div class="judul">
    LAPORAN PENJUALAN
</div>

<!-- INFO FILTER -->
@if(request('tanggal') || request('kategori') || request('metode_bayar'))
<p>
    @if(request('tanggal'))
        Periode: {{ request('tanggal') }} <br>
    @endif

    @if(request('kategori'))
        Kategori: {{ request('kategori') }} <br>
    @endif

    @if(request('metode_bayar'))
        Metode: {{ ucfirst(request('metode_bayar')) }}
    @endif
</p>
@endif

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Invoice</th>
            <th>Pelanggan</th>
            <th>Kategori</th>
            <th>Total</th>
            <th>Metode</th>
        </tr>
    </thead>
    <tbody>
        @foreach($penjualan as $p)
        <tr>
            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d-m-Y') }}</td>
            <td>{{ $p->kode_transaksi }}</td>
            <td>{{ $p->pelangganRel->nama_pelanggan ?? '-' }}</td>
            <td>{{ $p->kategoriRel->nama_kategori ?? '-' }}</td>
            <td class="text-right">Rp {{ number_format($p->total,0,',','.') }}</td>
            <td>{{ ucfirst($p->metode_bayar) }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4">TOTAL</th>
            <th class="text-right">
                Rp {{ number_format($penjualan->sum('total'),0,',','.') }}
            </th>
            <th></th>
        </tr>
    </tfoot>
</table>

<br><br>

<!-- FOOTER -->
<div style="text-align:right;">
    Banyuwangi, {{ date('d M Y') }}
</div>

</body>
</html>