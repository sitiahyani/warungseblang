@extends('layouts.admin')
@section('page_title','Data Diskon')
@section('page_subtitle','Manajemen Diskon')
@section('content')

<div class="container-fluid">
    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input
            type="text"
            id="searchDiskon"
            class="form-control"
            placeholder="Cari diskon..."
            style="width:260px"
        >
        <button
            class="btn btn-primary shadow-sm"
            data-toggle="modal"
            data-target="#modalTambah">
            <i class="fas fa-plus mr-2"></i>
            Tambah Diskon
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
                            <th>Nama Diskon</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th>Masa Aktif</th>
                            <th>Status</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableDiskon">
                        @forelse($diskon as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_diskon }}</td>
                            <td>{{ ucfirst($item->tipe_diskon) }}</td>
                            <td>
                                {{ $item->nilai_diskon }}
                                {{ $item->tipe_diskon == 'persen' ? '%' : '' }}
                            </td>
                            <td>
                                @if($item->masa_aktif_tipe == 'hari')
                                    {{ $item->masa_aktif_nilai }} Hari
                                @elseif($item->masa_aktif_tipe == 'bulan')
                                    {{ $item->masa_aktif_nilai }} Bulan
                                @elseif($item->masa_aktif_tipe == 'pesanan')
                                    {{ $item->masa_aktif_nilai }} Pesanan
                                @else
                                    Tanpa Batas
                                @endif

                            </td>
                            {{-- STATUS --}}
                            <td>
                                <div class="custom-control custom-switch">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input toggle-status"
                                        id="status{{ $item->id_diskon }}"
                                        data-id="{{ $item->id_diskon }}"
                                        {{ $item->status == 'aktif' ? 'checked' : '' }}>
                                    <label
                                        class="custom-control-label"
                                        for="status{{ $item->id_diskon }}">
                                    </label>
                                </div>
                            </td>
                           
                            {{-- AKSI --}}
                            <td class="text-center">
                                <button
                                    class="btn btn-sm btn-outline-warning"
                                    data-toggle="modal"
                                    data-target="#modalEdit{{ $item->id_diskon }}">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <form
                                    action="{{ route('diskon.destroy',$item->id_diskon) }}"
                                    method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Yakin hapus diskon ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data diskon
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
        <form action="{{ route('diskon.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Diskon</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Diskon</label>
                        <input type="text" name="nama_diskon" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Tipe Diskon</label>
                        <select name="tipe_diskon" class="form-control">
                            <option value="persen">Persentase</option>
                            <option value="nominal">Nominal</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nilai Diskon</label>
                        <input type="number" name="nilai_diskon" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Masa Aktif</label>
                        <select name="masa_aktif_tipe" class="form-control">
                            <option value="">Tanpa Batas</option>
                            <option value="hari">Hari</option>
                            <option value="bulan">Bulan</option>
                            <option value="pesanan">Jumlah Pesanan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nilai Masa Aktif</label>
                        <input
                            type="number"
                            name="masa_aktif_nilai"
                            class="form-control"
                            placeholder="contoh: 30 hari / 1 bulan / 10 pesanan">
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
                    <button class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL EDIT ================= --}}
@foreach($diskon as $item)
<div class="modal fade" id="modalEdit{{ $item->id_diskon }}">
    <div class="modal-dialog">
        <form action="{{ route('diskon.update',$item->id_diskon) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Diskon</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Diskon</label>
                        <input
                            type="text"
                            name="nama_diskon"
                            value="{{ $item->nama_diskon }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tipe</label>
                        <select name="tipe_diskon" class="form-control">
                            <option value="persen"
                            {{ $item->tipe_diskon=='persen'?'selected':'' }}>
                                Persentase
                            </option>
                            <option value="nominal"
                            {{ $item->tipe_diskon=='nominal'?'selected':'' }}>
                                Nominal
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nilai Diskon</label>
                        <input
                            type="number"
                            name="nilai_diskon"
                            value="{{ $item->nilai_diskon }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Masa Aktif</label>
                        <select name="masa_aktif_tipe" class="form-control">
                            <option value="">Tanpa batas</option>
                            <option value="hari"
                            {{ $item->masa_aktif_tipe=='hari'?'selected':'' }}>
                                Hari
                            </option>
                            <option value="bulan"
                            {{ $item->masa_aktif_tipe=='bulan'?'selected':'' }}>
                                Bulan
                            </option>
                            <option value="pesanan"
                            {{ $item->masa_aktif_tipe=='pesanan'?'selected':'' }}>
                                Jumlah Pesanan
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nilai Masa Aktif</label>
                        <input
                            type="number"
                            name="masa_aktif_nilai"
                            value="{{ $item->masa_aktif_nilai }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif"
                            {{ $item->status=='aktif'?'selected':'' }}>
                                Aktif
                            </option>
                            <option value="nonaktif"
                            {{ $item->status=='nonaktif'?'selected':'' }}>
                                Nonaktif
                            </option>
                        </select>
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

{{-- ================= MODAL STATUS ================= --}}
<div class="modal fade" id="modalStatus">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Konfirmasi Status</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                Yakin ingin mengubah status diskon ini?
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">
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
    $('#searchDiskon').on('keyup',function(){
        let value = $(this).val().toLowerCase()
        $('#tableDiskon tr').filter(function(){
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            )
        })
    })

    // TOGGLE STATUS
    let selectedToggle
    $('.toggle-status').on('change',function(){
        selectedToggle = $(this)
        let id = $(this).data('id')
        $('#formToggleStatus').attr(
            'action',
            '/admin/diskon/'+id+'/toggle-status'
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