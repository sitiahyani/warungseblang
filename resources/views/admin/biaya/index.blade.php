@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between mb-3">
        <h4 class="font-weight-bold">Biaya Pengeluaran</h4>

        <button class="btn btn-primary"
                data-toggle="modal"
                data-target="#modalTambah">
            <i class="fas fa-plus"></i> Tambah Biaya
        </button>
    </div>

    {{-- SEARCH --}}
    <form method="GET" class="mb-3">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Cari keterangan..."
               value="{{ request('search') }}">
    </form>

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
                        <th>Tanggal</th>
                        <th>Akun</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengeluaran as $p)
                    <tr>
                        <td>{{ $p->tanggal }}</td>
                        <td>{{ $p->kode_akun }} - {{ $p->nama_akun }}</td>
                        <td>Rp {{ number_format($p->jumlah,0,',','.') }}</td>
                        <td>{{ $p->keterangan }}</td>
                        <td>

                            {{-- EDIT --}}
                             <button class="btn btn-sm btn-outline-warning mr-1"
                                    data-toggle="modal"
                                    data-target="#edit{{ $p->id_pengeluaran }}">
                                
                                <i class="fas fa-pen"></i>
                            </button>

                            {{-- DELETE --}}
                            <form action="{{ route('biaya.delete',$p->id_pengeluaran) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Hapus biaya?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                     <i class="fas fa-trash"></i>
                                </button>
                            </form>

                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="edit{{ $p->id_pengeluaran }}">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('biaya.update',$p->id_pengeluaran) }}"
                                      method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5>Edit Biaya</h5>
                                        <button type="button"
                                                class="close"
                                                data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body row">

                                        <div class="col-md-6 mb-3">
                                            <label>Tanggal</label>
                                            <input type="date"
                                                   name="tanggal"
                                                   value="{{ $p->tanggal }}"
                                                   class="form-control">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Akun</label>
                                            <select name="id_akun"
                                                    class="form-control">
                                                @foreach($akun as $a)
                                                    <option value="{{ $a->id_akun }}"
                                                        {{ $p->id_akun == $a->id_akun ? 'selected' : '' }}>
                                                        {{ $a->nama_akun }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Jumlah</label>
                                            <input type="number"
                                                   name="jumlah"
                                                   value="{{ $p->jumlah }}"
                                                   class="form-control">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Keterangan</label>
                                            <input type="text"
                                                   name="keterangan"
                                                   value="{{ $p->keterangan }}"
                                                   class="form-control">
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="5"
                            class="text-center text-muted py-4">
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

            <form action="{{ route('biaya.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5>Tambah Biaya</h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body row">

                    <div class="col-md-6 mb-3">
                        <label>Tanggal</label>
                        <input type="date"
                               name="tanggal"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Akun</label>
                        <select name="id_akun"
                                class="form-control"
                                required>
                            @foreach($akun as $a)
                                <option value="{{ $a->id_akun }}">
                                    {{ $a->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Jumlah</label>
                        <input type="number"
                               name="jumlah"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Keterangan</label>
                        <input type="text"
                               name="keterangan"
                               class="form-control">
                    </div>

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

@endsection
