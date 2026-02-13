@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h4 class="mb-4">Stok Barang</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barang as $item)
                    <tr>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->stok }}</td>
                        <td class="text-center">

                            <button class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#editStok{{ $item->id_barang }}">
                                <i class="fas fa-pen"></i>
                            </button>

                        </td>
                    </tr>

                    {{-- MODAL EDIT STOK --}}
                    <div class="modal fade"
                         id="editStok{{ $item->id_barang }}">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content">

                                <form action="{{ route('stok.update',$item->id_barang) }}"
                                      method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5>Edit Stok</h5>
                                    </div>

                                    <div class="modal-body">
                                        <label>Stok Sekarang</label>
                                        <input type="number"
                                               name="stok"
                                               class="form-control"
                                               value="{{ $item->stok }}">
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-primary">
                                            Simpan
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
