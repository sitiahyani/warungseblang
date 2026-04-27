@extends('layouts.admin')

@section('page_title','Data Shift')
@section('page_subtitle','Manajemen Shift')

@section('content')
<div class="container-fluid">

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="searchShift" class="form-control" placeholder="Cari shift..." style="width:260px;">

        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
            + Tambah Shift
        </button>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Shift</th>
                        <th>Karyawan</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody id="tableShift">
                    @forelse ($shift as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->kode }}</td>
                        <td>{{ $item->nama_shift }}</td>

                        {{-- tampilkan nama karyawan --}}
                        <td>{{ $item->karyawan->nama_karyawan ?? '-' }}</td>

                        <td>{{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</td>

                        {{-- STATUS --}}
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                    class="custom-control-input toggle-status"
                                    id="status{{ $item->id_shift }}"
                                    data-id="{{ $item->id_shift }}"
                                    {{ $item->status == 'buka' ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                    for="status{{ $item->id_shift }}"></label>
                            </div>
                        </td>

                        {{-- AKSI --}}
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning"
                                data-toggle="modal"
                                data-target="#modalEdit{{ $item->id_shift }}">
                                <i class="fas fa-pen"></i>
                            </button>

                            <form action="{{ route('shift.destroy', $item->id_shift) }}"
                                method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Hapus shift ini?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada data shift
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL TAMBAH ================= -->
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <form action="{{ route('shift.store') }}" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Tambah Shift</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode</label>
                        <input type="text" name="kode" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Shift</label>
                        <input type="text" name="nama_shift" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Karyawan</label>
                        <select name="id_karyawan" class="form-control" required>
                            <option value="">Pilih Karyawan</option>
                            @foreach($karyawan as $k)
                                <option value="{{ $k->id_karyawan }}">
                                    {{ $k->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
@foreach($shift as $item)
<div class="modal fade" id="modalEdit{{ $item->id_shift }}">
    <div class="modal-dialog">
        <form action="{{ route('shift.update', $item->id_shift) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Shift</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode</label>
                        <input type="text" name="kode" value="{{ $item->kode }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Shift</label>
                        <input type="text" name="nama_shift" value="{{ $item->nama_shift }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Karyawan</label>
                        <select name="id_karyawan" class="form-control" required>
                            @foreach($karyawan as $k)
                                <option value="{{ $k->id_karyawan }}"
                                    {{ $item->id_karyawan == $k->id_karyawan ? 'selected' : '' }}>
                                    {{ $k->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" value="{{ $item->waktu_mulai }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" value="{{ $item->waktu_selesai }}" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-warning">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection