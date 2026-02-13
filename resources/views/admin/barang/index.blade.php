@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold mb-1">Data Barang</h4>
            <small class="text-muted">Manajemen Menu Warung Seblang</small>
        </div>

        <button class="btn btn-primary shadow-sm"
                data-toggle="modal"
                data-target="#modalTambah">
            <i class="fas fa-plus mr-2"></i> Tambah Barang
        </button>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Tipe</th>
                            <th>Nama</th>
                            <th>HPP</th>
                            <th>Harga Jual</th>
                            <th>Margin</th>
                            <th>stok</th>
                            <th>Status</th>
                            <th width="130">Aksi</th>
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
                                         class="rounded">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>{{ $item->kode_barang }}</td>

                            <td>{{ $item->kategori->nama_kategori }}</td>

                            <td>{{ $item->tipe->nama_tipe ?? '-' }}</td>

                            <td>{{ $item->nama_barang }}</td>
                            
                            <td>
    Rp {{ number_format($item->hpp ?? 0,0,',','.') }}
</td>

<td>
    @if($item->harga_jual)
        Rp {{ number_format($item->harga_jual,0,',','.') }}
    @else
        <span class="badge badge-warning">Belum ditentukan</span>
    @endif
</td>

@php
    $margin = ($item->harga_jual ?? 0) - ($item->hpp ?? 0);
@endphp

<td>
    <span class="{{ $margin < 0 ? 'text-danger' : 'text-success' }}">
        Rp {{ number_format($margin,0,',','.') }}
    </span>
</td>
<td>{{ $item->stok }}</td>


                            {{-- STATUS --}}
                            <td>
                                <form action="{{ route('barang.status',$item->id_barang) }}"
                                      method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="status{{ $item->id_barang }}"
                                               onchange="this.form.submit()"
                                               {{ $item->status == 'aktif' ? 'checked' : '' }}>
                                        <label class="custom-control-label"
                                               for="status{{ $item->id_barang }}"></label>
                                    </div>
                                </form>
                            </td>

                            {{-- AKSI --}}
                            <td>
    <a href="{{ route('barang.hpp',$item->id_barang) }}"
       class="btn btn-sm btn-info mr-1"
       title="Hitung HPP">
        <i class="fas fa-calculator"></i>
    </a>

    <button class="btn btn-sm btn-outline-warning mr-1"
            data-toggle="modal"
            data-target="#edit{{ $item->id_barang }}">
        <i class="fas fa-edit"></i>
    </button>

    <form action="{{ route('barang.destroy',$item->id_barang) }}"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('Hapus barang?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</td>

                        

                        {{-- ================= MODAL EDIT ================= --}}
                        <div class="modal fade" id="edit{{ $item->id_barang }}">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">

                                    <form action="{{ route('barang.update',$item->id_barang) }}"
                                          method="POST"
                                          enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Barang</h5>
                                            <button type="button"
                                                    class="close"
                                                    data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row">

                                                <div class="col-md-6 mb-3">
                                                    <label>Kategori</label>
                                                    <select name="id_kategori"
                                                            class="form-control"
                                                            required>
                                                        @foreach($kategori as $k)
                                                            <option value="{{ $k->id_kategori }}"
                                                                {{ $item->id_kategori == $k->id_kategori ? 'selected' : '' }}>
                                                                {{ $k->nama_kategori }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Tipe (Opsional)</label>
                                                    <select name="id_tipe"
                                                            class="form-control">
                                                        <option value="">-- Tanpa Tipe --</option>
                                                        @foreach($tipe as $t)
                                                            <option value="{{ $t->id_tipe }}"
                                                                {{ $item->id_tipe == $t->id_tipe ? 'selected' : '' }}>
                                                                {{ $item->id_tipe == $t->id_tipe ? '' : '' }}
                                                                {{ $t->nama_tipe }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Nama Barang</label>
                                                    <input type="text"
                                                           name="nama_barang"
                                                           class="form-control"
                                                           value="{{ $item->nama_barang }}"
                                                           required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Kode Barang</label>
                                                    <input type="text"
                                                           name="kode_barang"
                                                           class="form-control"
                                                           value="{{ $item->kode_barang }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Satuan</label>
                                                    <input type="text"
                                                           name="satuan"
                                                           class="form-control"
                                                           value="{{ $item->satuan }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                <label>HPP</label>
                                                <input type="number"
                                                    name="hpp"
                                                    class="form-control"
                                                    value="{{ $item->hpp ?? 0 }}"
                                                    readonly>

                                                <div class="mt-2">
                                                    <a href="{{ route('barang.hpp',$item->id_barang) }}"
                                                    class="btn btn-info btn-sm">
                                                        <i class="fas fa-calculator mr-1"></i>
                                                        Hitung HPP
                                                    </a>
                                                </div>
                                            </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Harga Jual</label>
                                                    <input type="number"
                                                           name="harga_jual"
                                                           class="form-control"
                                                           value="{{ $item->harga_jual }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label>Stok Awal</label>
                                                    <input type="number"
                                                        name="stok"
                                                        class="form-control"
                                                        value="0"
                                                        min="0">
                                                </div>

                                                <div class="col-12 mb-3">
                                                    <label>Keterangan</label>
                                                    <textarea name="keterangan"
                                                              class="form-control"
                                                              rows="3">{{ $item->keterangan }}</textarea>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Gambar</label>
                                                    <input type="file"
                                                           name="gambar"
                                                           class="form-control">
                                                </div>

                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- ================================================= --}}

                        @empty
                        <tr>
                            <td colspan="9"
                                class="text-center text-muted py-4">
                                Belum ada data barang
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('barang.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Barang</h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Kategori</label>
                            <select name="id_kategori"
                                    class="form-control"
                                    required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $k)
                                    <option value="{{ $k->id_kategori }}">
                                        {{ $k->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Tipe (Opsional)</label>
                            <select name="id_tipe"
                                    class="form-control">
                                <option value="">-- Tanpa Tipe --</option>
                                @foreach($tipe as $t)
                                    <option value="{{ $t->id_tipe }}">
                                        {{ $t->nama_tipe }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Nama Barang</label>
                            <input type="text"
                                   name="nama_barang"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Kode Barang</label>
                            <input type="text"
                                   name="kode_barang"
                                   class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Satuan</label>
                            <input type="text"
                                   name="satuan"
                                   class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>HPP</label>
                            <input type="number"
                                name="hpp"
                                class="form-control"
                                value="0"
                                readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Harga Jual</label>
                            <input type="number"
                                   name="harga_jual"
                                   class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Stok Awal</label>
                            <input type="number"
                                name="stok"
                                class="form-control"
                                value="0"
                                min="0">
                        </div>

                        <div class="col-12 mb-3">
                            <label>Keterangan</label>
                            <textarea name="keterangan"
                                      class="form-control"
                                      rows="3"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Gambar</label>
                            <input type="file"
                                   name="gambar"
                                   class="form-control">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
{{-- ================================================= --}}

{{-- SEARCH SCRIPT --}}
<script>
document.getElementById('searchBarang')
    .addEventListener('keyup', function () {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tableBarang tbody tr');

        rows.forEach(row => {
            row.style.display =
                row.innerText.toLowerCase().includes(value)
                    ? ''
                    : 'none';
        });
    });
</script>

@endsection

<div id="formResep" style="display:none;" class="col-12 mt-3">

    <hr>

    <h6 class="font-weight-bold mb-3">
        <i class="fas fa-utensils"></i> Resep / Komposisi Bahan
    </h6>

    <div id="resepWrapper"></div>

    <button type="button"
            class="btn btn-sm btn-secondary mt-2"
            onclick="tambahBahan()">
        + Tambah Bahan
    </button>

    <hr>

    <h5>Total HPP: Rp <span id="totalHpp">0</span></h5>

    <div class="col-md-6 mb-3">
        <label>HPP</label>
        <input type="number"
               name="hpp"
               id="hpp"
               class="form-control"
               readonly
               value="0">
    </div>

    <div class="col-md-6 mb-3 d-flex align-items-end">
        <button type="button"
                class="btn btn-info"
                onclick="toggleResep()">
            <i class="fas fa-calculator mr-1"></i>
            Hitung HPP
        </button>
    </div>

    <script>
        function toggleResep() {
            let form = document.getElementById('formResep');
            form.style.display =
                form.style.display === 'none'
                    ? 'block'
                    : 'none';
        }

        function tambahBahan() {
            let wrapper = document.getElementById('resepWrapper');

            wrapper.innerHTML += `
                <div class="row mb-2">
                    <div class="col-md-5">
                        <select class="form-control bahan" onchange="hitung()">
                            @foreach($bahan_baku as $b)
                                <option value="{{ $b->harga_per_satuan ?? 0 }}">
                                    {{ $b->nama_bahan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number"
                               class="form-control qty"
                               placeholder="Qty"
                               onkeyup="hitung()">
                    </div>
                </div>
            `;
        }

        function hitung() {
            let total = 0;

            document.querySelectorAll('#resepWrapper .row')
                .forEach(function (row) {
                    let harga = parseFloat(
                        row.querySelector('.bahan').value
                    ) || 0;

                    let qty = parseFloat(
                        row.querySelector('.qty').value
                    ) || 0;

                    total += harga * qty;
                });

            document.getElementById('totalHpp').innerText =
                total.toLocaleString('id-ID');

            document.getElementById('hpp').value = total;
        }
    </script>

</div>
