@extends('layouts.admin')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Stok Opname Bahan Baku</h4>

        <button class="btn btn-warning"
                data-toggle="modal"
                data-target="#modalSesuaikan">
            <i class="fas fa-sync-alt"></i> Sesuaikan Stok
        </button>
    </div>

    <div class="card-body">

        {{-- ALERT --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- FORM INPUT --}}
        <form action="{{ route('stok-opname.simpan') }}" method="POST" class="mb-4">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <label>Bahan</label>
                    <select name="id_bahan" class="form-control" required>
                        <option value="">-- Pilih Bahan --</option>
                        @foreach($bahan as $item)
                           <option value="{{ $item->id_bahan }}">
                                {{ $item->nama_bahan }} (stok sistem: {{ rtrim(rtrim($item->stok, '0'), '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Stok Fisik</label>
                    <input type="number"
                           step="0.01"
                           name="stok_fisik"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-3">
                    <label>Keterangan</label>
                    <input type="text"
                           name="keterangan"
                           class="form-control"
                           placeholder="contoh: rusak / susut">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </form>

        <hr>

        {{-- TABEL HASIL OPNAME --}}
        <h5 class="mb-3">Hasil Stok Opname</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Bahan</th>
                        <th>Stok Sistem</th>
                        <th>Stok Fisik</th>
                        <th>Selisih</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokOpname as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->bahan->nama_bahan ?? '-' }}</td>
                        <td>{{ rtrim(rtrim($item->stok_sistem, '0'), '.') }}</td>
                        <td>{{ rtrim(rtrim($item->stok_fisik, '0'), '.') }}</td>
                        <td>
                            @if($item->selisih < 0)
                                <span class="text-danger">
                                    {{ rtrim(rtrim($item->selisih, '0'), '.') }}
                                </span>
                            @else
                                <span class="text-success">
                                    +{{ rtrim(rtrim($item->selisih, '0'), '.') }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $item->keterangan }}</td>
                        <td>
                            @if($item->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-success">Sesuai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data stok opname</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL WARNING SESUAIKAN STOK --}}
<div class="modal fade" id="modalSesuaikan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold">Sesuaikan Stok</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <ol class="pl-3">
                    <li>Pastikan tidak ada transaksi yang sedang berjalan.</li>
                    <li>Penyesuaian ini akan mengubah stok bahan baku utama.</li>
                    <li>Data HPP akan menggunakan stok terbaru setelah penyesuaian.</li>
                </ol>

                <p class="mt-3 mb-0">
                    Apakah Anda yakin ingin menyesuaikan stok fisik dengan stok sistem saat ini?
                </p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Tidak</button>

                <form action="{{ route('stok-opname.sesuaikan') }}" method="POST">
                    @csrf
                    <button class="btn btn-success">
                        <i class="fas fa-check"></i> Ya
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection