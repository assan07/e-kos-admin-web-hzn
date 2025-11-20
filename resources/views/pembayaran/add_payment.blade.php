@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="mb-4">Tambah Pembayaran Baru</h3>

    {{-- Notifikasi sukses / error --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.pembayaran.add') }}" method="POST">
                @csrf

                {{-- NAMA --}}
                <div class="mb-3">
                    <label class="form-label">Nama Penyewa</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>

                {{-- KOS --}}
                <div class="mb-3">
                    <label class="form-label">Nama Kos</label>
                    <input type="text" name="kos" class="form-control" required>
                </div>

                {{-- KAMAR --}}
                <div class="mb-3">
                    <label class="form-label">Nomor Kamar</label>
                    <input type="text" name="kamar" class="form-control" required>
                </div>

                {{-- BULAN --}}
                <div class="mb-3">
                    <label class="form-label">Bulan Pembayaran</label>
                    <input type="text" name="bulan" class="form-control" placeholder="contoh: Januari 2025" required>
                </div>

                {{-- HARGA --}}
                <div class="mb-3">
                    <label class="form-label">Nominal Harga</label>
                    <input type="number" name="harga" class="form-control" required>
                </div>

                {{-- BUKTI foto --}}
                <div class="mb-3">
                    <label class="form-label">Bukti Pembayaran </label>
                    <input type="file" name="bukti_url" class="form-control" ">
                </div>

                {{-- BUTTON --}}
                <button type="submit" class="btn btn-primary px-4">
                    Simpan Pembayaran
                </button>

                <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary ms-2">
                    Kembali
                </a>

            </form>
        </div>
    </div>

</div>
@endsection
