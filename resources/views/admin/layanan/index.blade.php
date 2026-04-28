@extends('layouts.admin')

@section('page_title','Data Layanan')
@section('page_subtitle','Manajemen layanan Warung Seblang')

@section('content')

<div class="container-fluid">

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER + SEARCH --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input
            type="text"
            id="searchLayanan"
            class="form-control"
            placeholder="Cari nama layanan..."
            style="width:260px"
        >
        <button
            class="btn btn-primary shadow-sm"
            data-toggle="modal"
            data-target="#modalTambah">
            <i class="fas fa-plus mr-2"></i>
            Tambah Layanan
        </button>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Layanan</th>
                            <th>Kategori</th>
                            <th>Tipe</th>
                            <th>Harga</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th width="90" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableLayanan">
                        @forelse($layanan as $l)

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $l->kode_layanan }}</td>
                                <td>{{ $l->nama_layanan }}</td>
                                <td>{{ $l->kategori->nama_kategori ?? '-' }}</td>
                                <td>{{ $l->tipe->nama_tipe ?? '-' }}</td>
                                <td>
                                    Rp {{ number_format($l->harga,0,',','.') }}
                                </td>
                                <td>{{ $l->deskripsi }}</td>

                                {{-- STATUS --}}
                                <td>
                                    <div class="custom-control custom-switch">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input toggle-status"
                                            id="status{{ $l->id_layanan }}"
                                            data-id="{{ $l->id_layanan }}"
                                            {{ $l->status == 'aktif' ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" for="status{{ $l->id_layanan }}"></label>
                                    </div>
                                </td>
                                {{-- AKSI --}}
                                <td class="text-center">
                                    <button
                                        class="btn btn-sm btn-outline-warning btn-edit"
                                        data-id="{{ $l->id_layanan }}"
                                        data-kode="{{ $l->kode_layanan }}"
                                        data-nama="{{ $l->nama_layanan }}"
                                        data-kategori="{{ $l->id_kategori }}"
                                        data-tipe="{{ $l->id_tipe }}"
                                        data-harga="{{ $l->harga }}"
                                        data-deskripsi="{{ $l->deskripsi }}"
                                        data-status="{{ $l->status }}"
                                        data-toggle="modal"
                                        data-target="#modalEdit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty

                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Belum ada data layanan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL TAMBAH ================= --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('layanan.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Layanan</h5>
                    <button type="button" class="close" data-dismiss="modal"> &times; </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Layanan</label>
                        <input type="text" name="kode_layanan" class="form-control" required >
                    </div>

                    <div class="form-group">
                        <label>Nama Layanan</label>
                        <input type="text" name="nama_layanan" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" class="form-control">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id_kategori }}">
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tipe</label>
                        <select name="id_tipe" class="form-control">
                            <option value="">-- Pilih Tipe --</option>
                            @foreach($tipe as $t)
                                <option value="{{ $t->id_tipe }}">
                                    {{ $t->nama_tipe }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea
                            name="deskripsi"
                            class="form-control">
                        </textarea>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"> Batal </button>
                    <button type="submit" class="btn btn-primary"> Simpan </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL EDIT ================= --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formEdit">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Layanan</h5>
                    <button type="button" class="close" data-dismiss="modal"> &times; </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode</label>
                        <input type="text" name="kode_layanan" id="edit_kode" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama_layanan" id="edit_nama" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" id="edit_kategori" class="form-control">
                            @foreach($kategori as $k)
                                <option value="{{ $k->id_kategori }}">
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tipe</label>
                        <select name="id_tipe" id="edit_tipe" class="form-control">
                            @foreach($tipe as $t)
                                <option value="{{ $t->id_tipe }}">
                                    {{ $t->nama_tipe }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" id="edit_harga" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL STATUS ================= --}}
<div class="modal fade" id="modalStatus">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Konfirmasi Status</h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal">
                        &times;
                </button>
            </div>
            <div class="modal-body text-center">
                Yakin ingin mengubah status layanan ini?
            </div>
            <div class="modal-footer">
                <button
                    class="btn btn-secondary"
                    data-dismiss="modal">
                    Batal
                </button>
                <form id="formToggleStatus" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary">
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
$(function(){
    // SEARCH
    $('#searchLayanan').on('keyup',function(){
        let value = $(this).val().toLowerCase()
        $('#tableLayanan tr').filter(function(){
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            )
        })
    })

    // EDIT MODAL
    $('.btn-edit').on('click',function(){
        let id = $(this).data('id')
        $('#formEdit').attr('action','/admin/layanan/'+id)
        $('#edit_kode').val($(this).data('kode'))
        $('#edit_nama').val($(this).data('nama'))
        $('#edit_kategori').val($(this).data('kategori'))
        $('#edit_tipe').val($(this).data('tipe'))
        $('#edit_harga').val($(this).data('harga'))
        $('#edit_deskripsi').val($(this).data('deskripsi'))
        $('#edit_status').val($(this).data('status'))
    })
    // TOGGLE STATUS
    let selectedToggle
    $('.toggle-status').on('change',function(){
        selectedToggle = $(this)
        let id = $(this).data('id')
        $('#formToggleStatus').attr(
            'action',
            '/admin/layanan/'+id+'/toggle-status'
        )
        $('#modalStatus').modal('show')
    })
    $('#modalStatus').on('hidden.bs.modal',function(){
        if(!$('#formToggleStatus').data('submitted')){
            selectedToggle.prop(
                'checked',
                !selectedToggle.prop('checked')
            )
        }
        $('#formToggleStatus').data('submitted',false)
    })
    $('#formToggleStatus').on('submit',function(){
        $(this).data('submitted',true)
    })
})
</script>
@endpush