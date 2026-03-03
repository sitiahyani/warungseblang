<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jurnal Umum</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .judul {
            text-align: center;
            margin-bottom: 5px;
        }

        .subjudul {
            text-align: center;
            font-size: 11px;
            margin-bottom: 20px;
        }

        .transaksi {
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 3px 0;
        }

        .right {
            text-align: right;
        }

        .garis {
            border-bottom: 1px solid #000;
            margin-top: 5px;
        }

        .bold {
            font-weight: bold;
        }

        .total-box {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>

<div class="judul">
    <h3>WARUNG SEBLANG</h3>
</div>

<div class="subjudul">
    JURNAL UMUM
</div>

@foreach($jurnals as $jurnal)

    <div class="transaksi">

        <div class="bold">
            {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d-m-Y') }}
            - {{ $jurnal->keterangan }}
        </div>

        <table>
            @foreach($jurnal->details as $detail)
            <tr>
                <td width="60%">
                    {{ $detail->akun->nama_akun ?? '-' }}
                </td>

                <td width="20%" class="right">
                    {{ $detail->debit > 0 ? number_format($detail->debit,0,',','.') : '' }}
                </td>

                <td width="20%" class="right">
                    {{ $detail->kredit > 0 ? number_format($detail->kredit,0,',','.') : '' }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="garis"></div>

    </div>

@endforeach

<div class="total-box bold">
    <table>
        <tr>
            <td width="60%">TOTAL</td>
            <td width="20%" class="right">
                {{ number_format($totalDebit,0,',','.') }}
            </td>
            <td width="20%" class="right">
                {{ number_format($totalKredit,0,',','.') }}
            </td>
        </tr>
    </table>
</div>

</body>
</html>