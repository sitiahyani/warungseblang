@extends('layouts.admin')

@section('page_title','Data Pajak')
@section('page_subtitle','Manajemen Pajak')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    <!-- HEADER + SEARCH -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <input type="text"
                   id="searchPajak"
                   class="form-control"
                   placeholder="Cari nama / tipe..."
                   style="width:260px;">
        </div>

        <button class="btn btn-primary shadow-sm"
                data-toggle="modal"
                data-target="#modalTambah">
            <i class="fas fa-plus mr-2"></i> Tambah Pajak
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
                            <th>Nama Pajak</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th>Status</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="tablePajak">
                        @forelse ($pajak as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_pajak }}</td>
                            <td>{{ ucfirst($item->tipe_pajak) }}</td>
                            <td>{{ $item->nilai_pajak }}{{ $item->tipe_pajak=='persen'?'%':'' }}</td>

                            <!-- STATUS -->
                            <td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox"
                                           class="custom-control-input toggle-status"
                                           id="status{{ $item->id_pajak }}"
                                           data-id="{{ $item->id_pajak }}"
                                           {{ $item->status=='aktif'?'checked':'' }}>
                                    <label class="custom-control-label"
                                           for="status{{ $item->id_pajak }}"></label>
                                </div>
                            </td>

                            <!-- AKSI -->
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning mr-1"
                                        data-toggle="modal"
                                        data-target="#modalEdit{{ $item->id_pajak }}">
                                    <i class="fas fa-pen"></i>
                                </button>

                                <form action="{{ route('pajak.destroy',$item->id_pajak) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Yakin hapus pajak ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6"
                                class="text-center text-muted py-4">
                                Belum ada data pajak
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
        <form action="{{ route('pajak.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pajak</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Pajak</label>
                        <input type="text" name="nama_pajak" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Pajak</label>
                        <select name="tipe_pajak" class="form-control" required>
                            <option value="persen">Persentase</option>
                            <option value="nominal">Nominal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nilai Pajak</label>
                        <input type="number" name="nilai_pajak" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
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
@foreach($pajak as $item)
<div class="modal fade" id="modalEdit{{ $item->id_pajak }}">
    <div class="modal-dialog">
        <form action="{{ route('pajak.update',$item->id_pajak) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Pajak</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Pajak</label>
                        <input type="text" name="nama_pajak" class="form-control" value="{{ $item->nama_pajak }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Pajak</label>
                        <select name="tipe_pajak" class="form-control" required>
                            <option value="persen" {{ $item->tipe_pajak=='persen'?'selected':'' }}>Persentase</option>
                            <option value="nominal" {{ $item->tipe_pajak=='nominal'?'selected':'' }}>Nominal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nilai Pajak</label>
                        <input type="number" name="nilai_pajak" class="form-control" value="{{ $item->nilai_pajak }}" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" {{ $item->status=='aktif'?'selected':'' }}>Aktif</option>
                            <option value="nonaktif" {{ $item->status=='nonaktif'?'selected':'' }}>Nonaktif</option>
                        </select>
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

<!-- ================= MODAL STATUS ================= -->
<div class="modal fade" id="modalStatus">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Konfirmasi Status</h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body text-center">
                Yakin ingin mengubah status pajak ini?
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-dismiss="modal">
                    Batal
                </button>

                <form id="formToggleStatus" method="POST">
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

    // ===== TOGGLE STATUS =====
    let selectedToggle;

    $('.toggle-status').on('change', function(){

        selectedToggle = $(this);
        let id = $(this).data('id');

        $('#formToggleStatus').attr(
            'action',
            '/admin/pajak/' + id + '/toggle-status'
        );

        $('#modalStatus').modal('show');
    });

    $('#modalStatus').on('hidden.bs.modal', function () {

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


    // ===== SEARCH =====
    $('#searchPajak').on('keyup', function() {

        let value = $(this).val().toLowerCase();

        $('#tablePajak tr').filter(function() {
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            );
        });

    });

});
</script>
@endpush