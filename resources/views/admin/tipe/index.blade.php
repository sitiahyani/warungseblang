@extends('layouts.admin')

@section('page_title','Tipe')
@section('page_subtitle','Manajemen Tipe')

@section('content')

<div class="container-fluid">

{{-- ALERT --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


{{-- HEADER --}}
<div class="d-flex justify-content-end mb-4">

    <button class="btn btn-primary shadow-sm"
            data-toggle="modal"
            data-target="#modalTambah"
            style="border-radius:10px;">
        <i class="fas fa-plus mr-2"></i> Tambah Tipe
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
                        <th>Kategori</th>
                        <th>Nama Tipe</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tipe as $item)
                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </td>

                        <td class="font-weight-semibold">
                            {{ $item->nama_tipe }}
                        </td>

                        <td class="text-center">

                            {{-- EDIT --}}
                            <button class="btn btn-sm btn-outline-warning mr-1"
                                    data-toggle="modal"
                                    data-target="#modalEdit{{ $item->id_tipe }}">
                                <i class="fas fa-pen"></i>
                            </button>

                            {{-- DELETE --}}
                            <form action="{{ route('tipe.destroy',$item->id_tipe) }}"
                                  method="POST"
                                  class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Yakin hapus tipe ini?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                            </form>

                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Belum ada data tipe
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
        <form action="{{ route('tipe.store') }}" method="POST">
            @csrf

            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold">
                    Tambah Tipe
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>Kategori</label>

                    <select name="id_kategori"
                            class="form-control"
                            required>

                        <option value="">-- Pilih Kategori --</option>

                        @foreach($kategori as $k)
                        <option value="{{ $k->id_kategori }}">
                            {{ $k->nama_kategori }}
                        </option>
                        @endforeach

                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Tipe</label>

                    <input type="text"
                           name="nama_tipe"
                           class="form-control"
                           placeholder="Contoh: Minuman Dingin"
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
@foreach($tipe as $item)

<div class="modal fade" id="modalEdit{{ $item->id_tipe }}">
    <div class="modal-dialog modal-sm modal-dialog-centered">

    <div class="modal-content border-0 shadow">

        <form action="{{ route('tipe.update',$item->id_tipe) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-header bg-light">

                <h5 class="modal-title font-weight-bold">
                    Edit Tipe
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>Kategori</label>

                    <select name="id_kategori"
                            class="form-control"
                            required>

                        @foreach($kategori as $k)

                        <option value="{{ $k->id_kategori }}"
                            {{ $item->id_kategori == $k->id_kategori ? 'selected' : '' }}>

                            {{ $k->nama_kategori }}

                        </option>

                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Tipe</label>
                    <input type="text"
                           name="nama_tipe"
                           value="{{ $item->nama_tipe }}"
                           class="form-control"
                           required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"> Batal </button>
                <button type="submit" class="btn btn-warning"> Update </button>
            </div>
        </form>
    </div>
</div>
</div>
@endforeach
@endsection