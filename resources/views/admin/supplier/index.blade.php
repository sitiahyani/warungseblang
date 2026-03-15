@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
             <h4 class="font-weight-bold mb-1" style="font-size: 25px;">Data Supplier</h4>
            <small class="text-muted">Manajemen supplier bahan baku</small>
        </div>

        <button class="btn btn-primary shadow-sm"
                data-toggle="modal"
                data-target="#modalTambah"
                style="border-radius:10px;">
            <i class="fas fa-plus mr-2"></i> Tambah Supplier
        </button>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- CARD --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Nama Supplier</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($supplier as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-semibold">
                                {{ $item->nama_supplier }}
                            </td>
                            <td>{{ $item->no_hp }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td class="text-center">

                                {{-- EDIT --}}
                                <button class="btn btn-sm btn-outline-warning mr-1"
                                        data-toggle="modal"
                                        data-target="#modalEdit{{ $item->id_supplier }}">
                                    <i class="fas fa-pen"></i>
                                </button>

                                {{-- DELETE --}}
                                <form action="{{ route('supplier.delete',$item->id_supplier) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Yakin hapus supplier ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada data supplier
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>


{{-- ========================= --}}
{{-- MODAL EDIT DI LUAR TABLE --}}
{{-- ========================= --}}

@foreach ($supplier as $item)
<div class="modal fade" id="modalEdit{{ $item->id_supplier }}">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow border-0">

            <form action="{{ route('supplier.update',$item->id_supplier) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold">
                        Edit Supplier
                    </h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Supplier</label>
                        <input type="text"
                               name="nama_supplier"
                               class="form-control"
                               value="{{ $item->nama_supplier }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text"
                               name="no_hp"
                               class="form-control"
                               value="{{ $item->no_hp }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat"
                                  class="form-control"
                                  rows="3"
                                  required>{{ $item->alamat }}</textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach


{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow border-0">

            <form action="{{ route('supplier.store') }}" method="POST">
                @csrf

                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold">
                        Tambah Supplier
                    </h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Supplier</label>
                        <input type="text"
                               name="nama_supplier"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text"
                               name="no_hp"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat"
                                  class="form-control"
                                  rows="3"
                                  required></textarea>
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

@endsection
