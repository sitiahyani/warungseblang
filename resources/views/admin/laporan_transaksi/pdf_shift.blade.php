<!DOCTYPE html>
<html>
<head>
    <title>Laporan Shift</title>
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
    LAPORAN SHIFT KASIR
</div>

<table>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Shift</th>
            <th>Kasir</th>
            <th>Waktu</th>
            <th>Cash Awal</th>
            <th>Cash Akhir</th>
            <th>Selisih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($laporan as $item)
        <tr>
            <td>{{ $item->shift->kode ?? '-' }}</td>
            <td>{{ $item->shift->nama_shift ?? '-' }}</td>
            <td>{{ $item->user->name ?? 'Kasir' }}</td>
            <td>{{ $item->shift->waktu_mulai ?? '-' }} - {{ $item->shift->waktu_selesai ?? '-' }}</td>
            <td>{{ number_format($item->cash_awal ?? 0,0,',','.') }}</td>
            <td>{{ number_format($item->cash_akhir ?? 0,0,',','.') }}</td>
            <td>{{ number_format($item->selisih ?? 0,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br><br>

<!-- FOOTER -->
<div style="text-align:right;">
    Banyuwangi, {{ date('d M Y') }}
</div>

</body>
</html>