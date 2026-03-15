@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-weight-bold mb-1" style="font-size: 25px;">Data Kode Akun</h4>

        <button class="btn btn-primary"
                data-toggle="modal"
                data-target="#modalTambah">
            <i class="fas fa-plus"></i> Tambah Akun
        </button>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="80">Kode</th>
                        <th>Nama Akun</th>
                        <th width="150">Jenis</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($akun as $a)
                    <tr>
                        <td>{{ $a->kode_akun }}</td>
                        <td>{{ $a->nama_akun }}</td>
                        <td>{{ ucfirst($a->jenis_akun) }}</td>
                        <td>

                            {{-- EDIT (SAMA KAYA STOK BARANG) --}}
                            <button class="btn btn-sm btn-outline-warning mr-1"
                                    data-toggle="modal"
                                    data-target="#edit{{ $a->id_akun }}">
                                <i class="fas fa-pen"></i>
                            </button>

                            {{-- DELETE --}}
                            <form action="{{ route('kode-akun.destroy',$a->id_akun) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Hapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="edit{{ $a->id_akun }}">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">

                                <form action="{{ route('kode-akun.update',$a->id_akun) }}"
                                      method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Kode Akun</h5>
                                        <button type="button"
                                                class="close"
                                                data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body row">

                                        <div class="col-md-6 mb-3">
                                            <label>Kode Akun</label>
                                            <input type="text"
                                                   name="kode_akun"
                                                   value="{{ $a->kode_akun }}"
                                                   class="form-control"
                                                   required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Nama Akun</label>
                                            <input type="text"
                                                   name="nama_akun"
                                                   value="{{ $a->nama_akun }}"
                                                   class="form-control"
                                                   required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Jenis Akun</label>
                                            <select name="jenis_akun"
                                                    class="form-control"
                                                    required>
                                                <option value="aset" {{ $a->jenis_akun=='aset'?'selected':'' }}>Aset</option>
                                                <option value="kewajiban" {{ $a->jenis_akun=='kewajiban'?'selected':'' }}>Kewajiban</option>
                                                <option value="modal" {{ $a->jenis_akun=='modal'?'selected':'' }}>Modal</option>
                                                <option value="pendapatan" {{ $a->jenis_akun=='pendapatan'?'selected':'' }}>Pendapatan</option>
                                                <option value="beban" {{ $a->jenis_akun=='beban'?'selected':'' }}>Beban</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Belum ada data
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('kode-akun.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kode Akun</h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body row">

                    <div class="col-md-6 mb-3">
                        <label>Kode Akun</label>
                        <input type="text"
                               name="kode_akun"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Nama Akun</label>
                        <input type="text"
                               name="nama_akun"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Jenis Akun</label>
                        <select name="jenis_akun"
                                class="form-control"
                                required>
                            <option value="aset">Aset</option>
                            <option value="kewajiban">Kewajiban</option>
                            <option value="modal">Modal</option>
                            <option value="pendapatan">Pendapatan</option>
                            <option value="beban">Beban</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection