@extends('layouts.admin')

@section('page_title','Kategori')
@section('page_subtitle','Manajemen Kategori')

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    {{-- HEADER --}}
    <div class="d-flex justify-content-end mb-4">

        <button class="btn btn-primary shadow-sm"
                data-toggle="modal"
                data-target="#modalTambah"
                style="border-radius:10px;">
            <i class="fas fa-plus mr-2"></i> Tambah Kategori
        </button>

    </div>

    {{-- CARD TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Nama Kategori</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategori as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-semibold">
                                {{ $item->nama_kategori }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning mr-1"
                                    data-toggle="modal"
                                    data-target="#modalEdit{{ $item->id_kategori }}">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <form action="{{ route('kategori.destroy',$item->id_kategori) }}"
                                    method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Yakin hapus kategori ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                Belum ada data kategori
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf

                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold">
                        Tambah Kategori
                    </h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text"
                               name="nama_kategori"
                               class="form-control"
                               placeholder="Contoh: Makanan"
                               required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
@foreach($kategori as $item)
<div class="modal fade" id="modalEdit{{ $item->id_kategori }}">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('kategori.update',$item->id_kategori) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold">Edit Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text"
                        name="nama_kategori"
                        value="{{ $item->nama_kategori }}"
                        class="form-control"
                        required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection