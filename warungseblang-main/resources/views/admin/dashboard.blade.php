@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, {{ auth()->user()->nama }}</p>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-info">
                <div class="card-body">
                    <h5>Total Karyawan</h5>
                    <h2>10</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-success">
                <div class="card-body">
                    <h5>Total Penjualan</h5>
                    <h2>Rp 0</h2>
                </div>
            </div>
        </div>
    </div>
@endsection
