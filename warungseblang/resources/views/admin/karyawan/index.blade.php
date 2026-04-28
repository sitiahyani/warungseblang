@extends('layouts.admin')

@section('page_title','Data Karyawan')
@section('page_subtitle','Manajemen pegawai Warung Seblang')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <!-- SEARCH KIRI -->
        <div>
            <input type="text"
                id="searchKaryawan"
                class="form-control"
                placeholder="Cari nama / email / no HP..."
                style="width: 260px;">
        </div>

        <!-- SEMUA TOMBOL DI KANAN -->
        <div class="d-flex gap-2">

            <a href="{{ url('/admin/karyawan/export/pdf') }}" class="btn btn-danger">PDF</a>
            <a href="{{ url('/admin/karyawan/export/excel') }}" class="btn btn-success">Excel</a>

            <button class="btn btn-primary shadow-sm"
                    data-toggle="modal"
                    data-target="#modalTambah">
                <i class="fas fa-plus mr-2"></i> Tambah Karyawan
            </button>

        </div>

    </div>


    <!-- TABLE -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>JK</th>
                            <th>Email</th>
                            <th>Jabatan</th>
                            <th>Tgl Masuk</th>
                            <th>Status</th>
                            <th width="80" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="tableKaryawan">
                        @forelse($karyawan as $index => $k)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($k->foto)
                                    <img src="{{ asset('storage/'.$k->foto) }}" 
                                    width="50"
                                        height="50"
                                        style="object-fit:cover;border-radius:6px;">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $k->nama_karyawan }}</td>
                            <td>{{ $k->no_hp ?? '-' }}</td>
                            <td>{{ $k->jenis_kelamin }}</td>
                            <td>{{ $k->email ?? '-' }}</td>
                            <td>{{ $k->jabatan ?? '-' }}</td>
                            <td>{{ $k->tanggal_masuk ?? '-' }}</td>
                            <td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox"
                                        class="custom-control-input toggle-status"
                                        id="status{{ $k->id_karyawan }}"
                                        data-id="{{ $k->id_karyawan }}"
                                        {{ $k->status == 'aktif' ? 'checked' : '' }}>
                                    <label class="custom-control-label"
                                        for="status{{ $k->id_karyawan }}">
                                    </label>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning btn-edit"
                                    data-id="{{ $k->id_karyawan }}"
                                    data-nama="{{ $k->nama_karyawan }}"
                                    data-hp="{{ $k->no_hp }}"
                                    data-jk="{{ $k->jenis_kelamin }}"
                                    data-email="{{ $k->email }}"
                                    data-jabatan="{{ $k->jabatan }}"
                                    data-toggle="modal"
                                    data-target="#modalEdit">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data karyawan
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
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST"
                action="{{ url('/admin/karyawan') }}"
                id="formTambah"
                enctype="multipart/form-data">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Karyawan</h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Foto</label>
                        <input type="file" name="foto" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Nama *</label>
                        <input type="text"
                               name="nama_karyawan"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text"
                               name="no_hp"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                                class="form-control">
                            <option value="">-- pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email"
                               name="email"
                               class="form-control">
                    </div>

                    <hr>

                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text"
                            name="jabatan"
                            class="form-control"
                            placeholder="Contoh: owner, kepala dapur, kasir...">
                    </div>

                    <h6>Akun Login (Opsional)</h6>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role"
                                class="form-control">
                            <option value="">-- Pilih Role --</option>
                            <option value="kasir">Kasir</option>
                            <option value="pelayan">Pelayan</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input name="username"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password"
                               name="password"
                               class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST" action="" id="formEdit" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Foto</label>
                        <input type="file" name="foto" id="edit_foto" class="form-control">                    </div>

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" id="edit_nama" name="nama_karyawan" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text" id="edit_hp" name="no_hp" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select id="edit_jk" name="jenis_kelamin" class="form-control">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="edit_email" name="email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" id="edit_jabatan" name="jabatan" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- ================= MODAL STATUS ================= -->
<div class="modal fade" id="modalStatus">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Konfirmasi Status</h5>
                <button type="button" class="close"
                        data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body text-center">
                Yakin ingin mengubah status karyawan ini?
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

    // ===== EDIT =====
    $(document).on('click', '.btn-edit', function(){

        let id = $(this).data('id');

        // 🔥 INI KUNCI UTAMA
        $('#formEdit').attr('action', '/admin/karyawan/' + id);

        // isi form
        $('#edit_nama').val($(this).data('nama'));
        $('#edit_hp').val($(this).data('hp'));
        $('#edit_jk').val($(this).data('jk'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_jabatan').val($(this).data('jabatan'));

    });

    // ===== TOGGLE STATUS =====
    let selectedToggle;

    $('.toggle-status').on('change', function(){

        selectedToggle = $(this);
        let id = $(this).data('id');

        $('#formToggleStatus').attr(
            'action',
            '/admin/karyawan/' + id + '/toggle-status'
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
    $('#searchKaryawan').on('keyup', function() {

        let value = $(this).val().toLowerCase();

        $('#tableKaryawan tr').filter(function() {
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            );
        });

    });

});

</script>
@endpush