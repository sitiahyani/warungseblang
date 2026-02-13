<!DOCTYPE html>
<html>
<head>
    <title>Jurnal Umum</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td {
            border:1px solid black;
            padding:5px;
            text-align:right;
        }
        th { background:#eee; }
        td.left { text-align:left; }
    </style>
</head>
<body>

<h3 style="text-align:center;">JURNAL UMUM</h3>

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Debit</th>
            <th>Kredit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jurnals as $jurnal)
            @foreach($jurnal->details as $detail)
            <tr>
                <td class="left">{{ $jurnal->tanggal }}</td>
                <td class="left">{{ $jurnal->keterangan }}</td>
                <td>{{ number_format($detail->debit,0,',','.') }}</td>
                <td>{{ number_format($detail->kredit,0,',','.') }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">TOTAL</th>
            <th>{{ number_format($totalDebit,0,',','.') }}</th>
            <th>{{ number_format($totalKredit,0,',','.') }}</th>
        </tr>
    </tfoot>
</table>

</body>
</html>
