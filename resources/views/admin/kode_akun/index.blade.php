@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h4 class="mb-4">Kode Akun</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Akun</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($akun as $item)
                    <tr>
                       <td>{{ $item->kode_akun }}</td>
                        <td>{{ $item->nama_akun }}</td>
                        <td>{{ ucfirst($item->jenis_akun) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
