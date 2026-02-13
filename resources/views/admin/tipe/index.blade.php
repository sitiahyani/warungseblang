@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="mb-1 font-weight-bold">Data Tipe</h4>
            <small class="text-muted">Manajemen tipe barang berdasarkan kategori</small>
        </div>

        <button class="btn btn-primary mt-3 mt-md-0 shadow-sm"
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
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Kategori</th>
                            <th>Nama Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipe as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge badge-light px-3 py-2">
                                    {{ $item->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="font-weight-semibold">
                                {{ $item->nama_tipe }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
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
                            <option value="{{ $k->id }}">
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
@endsection
