@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
          <h4 class="font-weight-bold mb-1" style="font-size: 25px;">Data Bahan Baku</h4>
            <small class="text-muted">Manajemen bahan baku Warung Seblang</small>
        </div>

        <button class="btn btn-primary shadow-sm"
                data-toggle="modal"
                data-target="#modalTambah"
                style="border-radius:10px;">
            <i class="fas fa-plus mr-2"></i> Tambah Bahan
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

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Nama Bahan</th>
                            <th>Satuan</th>
                            <th>Harga / Satuan</th>
                            <th>Stok</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bahan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-semibold">
                                {{ $item->nama_bahan }}
                            </td>
                            <td>{{ $item->satuan }}</td>
                            <td>
                                Rp {{ number_format($item->harga_per_satuan ?? 0,0,',','.') }}
                            </td>
                            <td>{{ $item->stok }}</td>
                            <td class="text-center">

                                {{-- EDIT --}}
                                <button class="btn btn-sm btn-outline-warning mr-1"
                                        data-toggle="modal"
                                        data-target="#modalEdit{{ $item->id_bahan }}">
                                    <i class="fas fa-pen"></i>
                                </button>

                                {{-- DELETE --}}
                                <form action="{{ route('bahan.delete',$item->id_bahan) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Yakin hapus bahan ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada data bahan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ================= MODAL EDIT ================= --}}
@foreach ($bahan as $item)
<div class="modal fade" id="modalEdit{{ $item->id_bahan }}">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow border-0">

            <form action="{{ route('bahan.update',$item->id_bahan) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold">
                        Edit Bahan
                    </h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Bahan</label>
                        <input type="text"
                               name="nama_bahan"
                               class="form-control"
                               value="{{ $item->nama_bahan }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text"
                               name="satuan"
                               class="form-control"
                               value="{{ $item->satuan }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Harga per Satuan</label>
                        <input type="number"
                               name="harga_per_satuan"
                               class="form-control"
                               value="{{ $item->harga_per_satuan }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number"
                               name="stok"
                               class="form-control"
                               value="{{ $item->stok }}"
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
                            class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach


{{-- ================= MODAL TAMBAH ================= --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow border-0">

            <form action="{{ route('bahan.store') }}" method="POST">
                @csrf

                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold">
                        Tambah Bahan
                    </h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Bahan</label>
                        <input type="text"
                               name="nama_bahan"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text"
                               name="satuan"
                               class="form-control"
                               placeholder="kg / gram / liter / pcs"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Harga per Satuan</label>
                        <input type="number"
                               name="harga_per_satuan"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number"
                               name="stok"
                               class="form-control"
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

@endsection
