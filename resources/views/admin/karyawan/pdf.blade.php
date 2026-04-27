<!DOCTYPE html>
<html>
<head>
    <title>Data Karyawan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .kop {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop h2 {
            margin: 0;
        }

        .kop p {
            margin: 2px 0;
            font-size: 12px;
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

        th {
            background-color: #f2f2f2;
        }

        .judul {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- 🔥 KOP -->
<div class="kop">
    <h2>WARUNG SEBLANG</h2>
    <p>jl Krajan Timur Desa, Krajan Timur, Padang, Kec. Singojuruh, Kabupaten Banyuwangi,</p>
    <p>Telp: 0812-3397-1802</p>
</div>

<!-- JUDUL -->
<div class="judul">
    LAPORAN DATA KARYAWAN
</div>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>No HP</th>
            <th>JK</th>
            <th>Email</th>
            <th>Jabatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($karyawan as $k)
        <tr>
            <td>{{ $k->nama_karyawan }}</td>
            <td>{{ $k->no_hp }}</td>
            <td>{{ $k->jenis_kelamin }}</td>
            <td>{{ $k->email }}</td>
            <td>{{ $k->jabatan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br><br>

<!-- FOOTER TANGGAL -->
<div style="text-align:right;">
    Banyuwangi, {{ date('d M Y') }}
</div>

</body>
</html>