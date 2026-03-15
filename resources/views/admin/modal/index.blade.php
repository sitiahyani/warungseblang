@extends('layouts.admin')

@section('page_title','Data Modal')
@section('page_subtitle','Manajemen Setoran & Penarikan Modal')

@section('content')

<div class="row">

    <!-- CARD SETOR -->
    <div class="col-md-4">
        <div class="card bg-success elevation-2">
            <div class="card-body">
                <h5>Total Setoran</h5>
                <h3>Rp {{ number_format($totalSetor,0,',','.') }}</h3>
            </div>
        </div>
    </div>

    <!-- CARD TARIK -->
    <div class="col-md-4">
        <div class="card bg-danger elevation-2">
            <div class="card-body">
                <h5>Total Penarikan</h5>
                <h3>Rp {{ number_format($totalTarik,0,',','.') }}</h3>
            </div>
        </div>
    </div>

    <!-- CARD SALDO -->
    <div class="col-md-4">
        <div class="card bg-primary elevation-2">
            <div class="card-body">
                <h5>Saldo Modal</h5>
                <h3>Rp {{ number_format($saldo,0,',','.') }}</h3>
            </div>
        </div>
    </div>

</div>

<div class="card mt-3">

    <div class="card-header d-flex align-items-center">

    <h4 class="mb-0">Data Modal</h4>

    <button class="btn btn-primary btn-sm ml-auto"
        data-toggle="modal"
        data-target="#modalTambah">
        <i class="fas fa-plus"></i> Tambah Modal
    </button>

</div>


    <div class="card-body table-responsive">

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($modal as $m)
                <tr>
                    <td>{{ $m->tanggal }}</td>
                    <td>
                        <span class="badge {{ $m->jenis=='tambah'?'badge-success':'badge-danger' }}">
                            {{ ucfirst($m->jenis) }}
                        </span>
                    </td>
                    <td>Rp {{ number_format($m->jumlah,0,',','.') }}</td>
                    <td>{{ $m->keterangan }}</td>
                    <td>
                         <button class="btn btn-sm btn-outline-warning mr-1"
                                    data-toggle="modal"
                                    data-target="#edit{{ $m->id_modal }}">
                                <i class="fas fa-pen"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

  


{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <form action="{{ route('modal.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Modal</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Jenis</label>
                        <select name="jenis" class="form-control" required>
                            <option value="tambah">Setor Modal</option>
                            <option value="tarik">Tarik Modal</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- MODAL EDIT --}}
@foreach($modal as $m)
<div class="modal fade" id="edit{{ $m->id_modal }}">
    <div class="modal-dialog">
        <form action="{{ route('modal.update',$m->id_modal) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Modal</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <input type="date" name="tanggal"
                        value="{{ $m->tanggal }}"
                        class="form-control mb-2" required>

                    <select name="jenis" class="form-control mb-2">
                        <option value="tambah" {{ $m->jenis=='tambah'?'selected':'' }}>Setor Modal</option>
                        <option value="tarik" {{ $m->jenis=='tarik'?'selected':'' }}>Tarik Modal</option>
                    </select>

                    <input type="number" name="jumlah"
                        value="{{ $m->jumlah }}"
                        class="form-control mb-2" required>

                    <textarea name="keterangan" class="form-control">
                        {{ $m->keterangan }}
                    </textarea>

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
