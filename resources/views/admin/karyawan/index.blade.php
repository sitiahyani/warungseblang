@extends('layouts.admin')

@section('page_title','Data Karyawan')
@section('page_subtitle','Manajemen pegawai Warung Seblang')

@section('content')
<div class="container-fluid">

    <!-- HEADER + SEARCH -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <input type="text"
                   id="searchKaryawan"
                   class="form-control"
                   placeholder="Cari nama / email / no HP..."
                   style="width: 260px;">
        </div>

        <button class="btn btn-primary shadow-sm"
                data-toggle="modal"
                data-target="#modalTambah">
            <i class="fas fa-plus mr-2"></i> Tambah Karyawan
        </button>
    </div>


    <!-- TABLE -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>JK</th>
                            <th>Email</th>
                            <th>Tgl Masuk</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody id="tableKaryawan">
                        @forelse($karyawan as $k)
                        <tr>
                            <td>{{ $k->nama_karyawan }}</td>
                            <td>{{ $k->no_hp ?? '-' }}</td>
                            <td>{{ $k->jenis_kelamin }}</td>
                            <td>{{ $k->email ?? '-' }}</td>
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
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
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

                    <h6>Akun Login (Opsional)</h6>

                    <div class="form-group">
                        <label>Role</label>
                        <select name="role"
                                class="form-control">
                            <option value="">-- Tidak dibuatkan akun --</option>
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
