<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembelian</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .judul {
            text-align: center;
            margin-bottom: 20px;
        }

        .nota {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 4px;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .garis-tebal {
            border-bottom: 2px solid #000;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="judul">
    <h3>WARUNG SEBLANG</h3>
    <h4>LAPORAN PEMBELIAN</h4>
</div>

@foreach($pembelians as $pembelian)

<div class="nota">

    <div class="bold">
        {{ \Carbon\Carbon::parse($pembelian->tanggal)->format('d-m-Y') }}
        | Supplier: {{ $pembelian->supplier->nama_supplier }}
        | {{ strtoupper($pembelian->metode_bayar) }}
    </div>

    <table>
        <thead>
            <tr class="border-bottom bold">
                <th width="40%">Bahan</th>
                <th width="10%">Qty</th>
                <th width="25%" class="right">Harga</th>
                <th width="25%" class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian->details as $detail)
            <tr>
                <td>{{ $detail->bahan->nama_bahan }}</td>
                <td>{{ $detail->qty }}</td>
                <td class="right">
                    {{ number_format($detail->harga,0,',','.') }}
                </td>
                <td class="right">
                    {{ number_format($detail->subtotal,0,',','.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-bottom"></div>

    <table>
        <tr class="bold">
            <td width="75%">TOTAL</td>
            <td width="25%" class="right">
                {{ number_format($pembelian->total,0,',','.') }}
            </td>
        </tr>
    </table>

    <div class="garis-tebal"></div>

</div>

@endforeach

<br>

<table>
    <tr class="bold">
        <td width="75%">TOTAL KESELURUHAN</td>
        <td width="25%" class="right">
            {{ number_format($total,0,',','.') }}
        </td>
    </tr>
</table>

</body>
</html>