@extends('layouts.admin')

@section('page_title','Data Shift')
@section('page_subtitle','Manajemen Shift')

@section('content')
<div class="container-fluid">

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    {{ $errors->first() }}
</div>
@endif


<!-- HEADER + SEARCH -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <input type="text"
               id="searchShift"
               class="form-control"
               placeholder="Cari shift..."
               style="width:260px;">
    </div>

    <button class="btn btn-primary shadow-sm"
            data-toggle="modal"
            data-target="#modalTambah">
        <i class="fas fa-plus mr-2"></i> Tambah Shift
    </button>

</div>

<!-- TABLE -->
<div class="card shadow-sm border-0">
<div class="card-body p-0">
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
            <td>{{ $item->karyawan->nama_karyawan ?? '-' }}</td>
            <td>{{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</td>

            <!-- STATUS -->
            <td>
                <div class="custom-control custom-switch">
                    <input type="checkbox"
                    class="custom-control-input toggle-status"
                    id="status{{ $item->id_shift }}"
                    data-id="{{ $item->id_shift }}"
                    {{ $item->status=='buka'?'checked':'' }}>
                    <label class="custom-control-label"
                        for="status{{ $item->id_shift }}">
                    </label>
                </div>
            </td>

            <!-- AKSI -->
            <td class="text-center">
                <button class="btn btn-sm btn-outline-warning mr-1"
                    data-toggle="modal"
                    data-target="#modalEdit{{ $item->id_shift }}">
                    <i class="fas fa-pen"></i>
                </button>
                <form action="{{ route('shift.destroy',$item->id_shift) }}"
                    method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Yakin hapus shift ini?')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </td>
        </tr>

        @empty
        <tr>
            <td colspan="6"
                class="text-center text-muted py-4">
                Belum ada data shift
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
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
            <h5 class="modal-title">Tambah Shift</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <label>Kode Shift</label>
                <input type="text"
                name="kode"
                class="form-control"
                required>
            </div>

            <div class="form-group">
                <label>Nama Shift</label>
                <input type="text"
                name="nama_shift"
                class="form-control"
                placeholder="Contoh: Shift Pagi"
                required>
            </div>

            <div class="form-group">
                <label>Karyawan</label>
                <select name="id_karyawan"
                    class="form-control"
                    required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawan as $k)
                    <option value="{{ $k->id_karyawan }}">
                        {{ $k->nama_karyawan }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Waktu Mulai</label>
                <input type="time"
                name="waktu_mulai"
                class="form-control"
                required>
            </div>

            <div class="form-group">
                <label>Waktu Selesai</label>
                <input type="time"
                name="waktu_selesai"
                class="form-control"
                required>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan"
                class="form-control"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-primary">
                Simpan
            </button>
        </div>
    </div>
</form>
</div>
</div>

<!-- ================= MODAL EDIT ================= -->
@foreach($shift as $item)
<div class="modal fade"
id="modalEdit{{ $item->id_shift }}">
<div class="modal-dialog">
<form action="{{ route('shift.update',$item->id_shift) }}"
    method="POST">
    @csrf
    @method('PUT')

    <div class="modal-content">
        <div class="modal-header">
            <h5>Edit Shift</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <label>Kode Shift</label>
                <input type="text"
                name="kode"
                class="form-control"
                value="{{ $item->kode }}"
                required>
            </div>

            <div class="form-group">
                <label>Nama Shift</label>
                <input type="text"
                name="nama_shift"
                class="form-control"
                value="{{ $item->nama_shift }}"
                required>
            </div>

            <div class="form-group">
                <label>Karyawan</label>
                <select name="id_karyawan"
                    class="form-control">
                    @foreach($karyawan as $k)
                    <option value="{{ $k->id_karyawan }}"
                        {{ $item->id_karyawan==$k->id_karyawan?'selected':'' }}>
                        {{ $k->nama_karyawan }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Waktu Mulai</label>
                <input type="time"
                name="waktu_mulai"
                class="form-control"
                value="{{ $item->waktu_mulai }}">
            </div>

            <div class="form-group">
                <label>Waktu Selesai</label>
                <input type="time"
                name="waktu_selesai"
                class="form-control"
                value="{{ $item->waktu_selesai }}">
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan"
                    class="form-control">
                    {{ $item->keterangan }}
                </textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-warning">
                Update
            </button>
        </div>
    </div>
</form>
</div>
</div>
@endforeach

<!-- ================= MODAL STATUS ================= -->
<div class="modal fade" id="modalStatus">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
    <div class="modal-header bg-warning">
        <h5 class="modal-title">Konfirmasi Status</h5>
        <button type="button"
            class="close"
            data-dismiss="modal">&times;
        </button>
    </div>

    <div class="modal-body text-center">
        Yakin ingin mengubah status shift ini?
    </div>

    <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">
            Batal
        </button>

        <form id="formToggleStatus"
            method="POST">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="btn btn-primary">
                Ya, Ubah
            </button>
        </form>
    </div>
</div>
</div>
</div>
@endsection
@push('scripts')

<script>
$(document).ready(function(){
    // ===== SEARCH =====
    $('#searchShift').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#tableShift tr').filter(function() {
            $(this).toggle(
                $(this).text()
                .toLowerCase()
                .indexOf(value) > -1
            );
        });
    });

    // ===== TOGGLE STATUS =====
    let selectedToggle;
    $('.toggle-status').on('change', function(){
        selectedToggle = $(this);
        let id = $(this).data('id');
        $('#formToggleStatus').attr(
            'action',
            '/admin/shift/' + id + '/toggle-status'
        );
        $('#modalStatus').modal('show');
    });
    $('#modalStatus').on('hidden.bs.modal', function(){
        if(!$('#formToggleStatus').data('submitted')){
            selectedToggle.prop(
                'checked',
                !selectedToggle.prop('checked')
            );
        }
        $('#formToggleStatus').data('submitted', false);
    });
    $('#formToggleStatus').on('submit', function(){

        $(this).data('submitted', true);
    });
});
</script>
@endpush