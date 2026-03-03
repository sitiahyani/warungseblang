@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold mb-1">Stok Barang</h4>
            <small class="text-muted">Manajemen Stok Menu Warung Seblang</small>
        </div>
    </div>

    {{-- CARD --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            {{-- SEARCH --}}
            <div class="mb-3">
                <input type="text"
                       id="searchStok"
                       class="form-control"
                       placeholder="Cari barang...">
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0"
                       id="tableStok">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Nama</th>
                            <th>Stok</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($barang as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                @if($item->gambar)
                                    <img src="{{ asset('storage/'.$item->gambar) }}"
                                         width="50"
                                         height="50"
                                         class="rounded"
                                         style="object-fit:cover;">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $item->nama_barang }}</td>

                            <td>
                                <span class="badge badge-primary px-3 py-2">
                                    {{ $item->stok }}
                                </span>
                            </td>

                            <td>
                                <button class="btn btn-sm btn-outline-warning"
                                        data-toggle="modal"
                                        data-target="#stok{{ $item->id_barang }}">
                                     <i class="fas fa-pen"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- ================= MODAL ================= --}}
                        <div class="modal fade"
                             id="stok{{ $item->id_barang }}">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">

                                    <form action="{{ route('stok.update',$item->id_barang) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title font-weight-bold">
                                                Ubah Stok Barang
                                            </h5>
                                            <button type="button"
                                                    class="close"
                                                    data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="row mb-4 align-items-center">

                                                <div class="col-md-3 text-center">
                                                    @if($item->gambar)
                                                        <img src="{{ asset('storage/'.$item->gambar) }}"
                                                             class="rounded-circle shadow"
                                                             width="110"
                                                             height="110"
                                                             style="object-fit:cover;">
                                                    @endif
                                                </div>

                                                <div class="col-md-9">

                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <strong>Nama:</strong>
                                                            {{ $item->nama_barang }} <br>

                                                            <strong>Kategori:</strong>
                                                            {{ $item->kategori->nama_kategori ?? '-' }}
                                                        </div>

                                                        <div class="col-md-6">
                                                            <strong>Kode:</strong>
                                                            {{ $item->kode_barang }} <br>

                                                            <strong>Sisa Stok:</strong>
                                                            <span class="badge badge-primary">
                                                                {{ $item->stok }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    {{-- CONTROL + - --}}
                                                    <div class="d-flex align-items-center">

                                                        <button type="button"
                                                                class="btn btn-success"
                                                                onclick="plus{{ $item->id_barang }}()">
                                                            <i class="fas fa-plus"></i>
                                                        </button>

                                                        <input type="number"
                                                               id="jumlah{{ $item->id_barang }}"
                                                               name="jumlah"
                                                               value="0"
                                                               min="0"
                                                               class="form-control mx-2 text-center"
                                                               style="width:100px;">

                                                        <button type="button"
                                                                class="btn btn-danger"
                                                                onclick="minus{{ $item->id_barang }}()">
                                                            <i class="fas fa-minus"></i>
                                                        </button>

                                                        <input type="hidden"
                                                               name="aksi"
                                                               id="aksi{{ $item->id_barang }}">
                                                    </div>

                                                </div>
                                            </div>

                                            <hr>

                                            {{-- HPP --}}
                                            <div class="mb-3">
                                                <label>HPP (Harga Dasar)</label>
                                                <input type="text"
                                                       class="form-control"
                                                       value="Rp {{ number_format($item->hpp ?? 0,0,',','.') }}"
                                                       readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label>Keterangan</label>
                                                <textarea name="keterangan"
                                                          class="form-control"
                                                          rows="3">{{ $item->keterangan }}</textarea>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit"
                                                    class="btn btn-primary"
                                                    onclick="setAksi{{ $item->id_barang }}()">
                                                Simpan Perubahan
                                            </button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

                        {{-- SCRIPT PER ITEM --}}
                        <script>
                        function plus{{ $item->id_barang }}() {
                            let input = document.getElementById('jumlah{{ $item->id_barang }}');
                            input.value = parseInt(input.value || 0) + 1;
                            document.getElementById('aksi{{ $item->id_barang }}').value = 'tambah';
                        }

                        function minus{{ $item->id_barang }}() {
                            let input = document.getElementById('jumlah{{ $item->id_barang }}');
                            if(parseInt(input.value) > 0){
                                input.value = parseInt(input.value) - 1;
                            }
                            document.getElementById('aksi{{ $item->id_barang }}').value = 'kurang';
                        }

                        function setAksi{{ $item->id_barang }}(){
                            if(!document.getElementById('aksi{{ $item->id_barang }}').value){
                                document.getElementById('aksi{{ $item->id_barang }}').value = 'tambah';
                            }
                        }
                        </script>
                        {{-- ========================================= --}}

                        @empty
                        <tr>
                            <td colspan="7"
                                class="text-center text-muted py-4">
                                Belum ada data stok
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

{{-- SEARCH SCRIPT --}}
<script>
document.getElementById('searchStok')
    .addEventListener('keyup', function () {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tableStok tbody tr');

        rows.forEach(row => {
            row.style.display =
                row.innerText.toLowerCase().includes(value)
                    ? ''
                    : 'none';
        });
    });
</script>

@endsection
