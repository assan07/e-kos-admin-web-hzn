@extends('layouts.app')
@section('title', 'Pembayaran-Kamar | Tambah-Pembayaran-Baru')


@section('content')
   <div class="container mt-4">

      <h3 class="mb-4">Tambah Pembayaran Baru</h3>

      {{-- Notifikasi --}}
      @if (session('success'))
         <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
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

            <form action="{{ route('admin.pembayaran.add') }}" method="POST" enctype="multipart/form-data">
               @csrf

               {{-- NAMA KOS --}}
               <div class="mb-3">
                  <label class="form-label">Nama Kos</label>
                  <input type="text" name="kos" class="form-control" required>
               </div>

               {{-- NAMA PENYEWA --}}
               <div class="mb-3">
                  <label class="form-label">Nama Penyewa</label>
                  <input type="text" name="nama" class="form-control" required>
               </div>

               {{-- NOMOR KAMAR --}}
               <div class="mb-3">
                  <label class="form-label">Nomor Kamar</label>
                  <input type="text" name="kamar" class="form-control" required>
               </div>

               {{-- BULAN --}}
               <div class="mb-3">
                  <label class="form-label">Bulan Pembayaran</label>
                  <input type="month" name="bulan" class="form-control" required>
               </div>

               {{-- HARGA --}}
               <div class="mb-3">
                  <label class="form-label">Nominal Harga</label>
                  <input type="number" name="harga" class="form-control" required>
               </div>

               {{-- BUKTI --}}
               <div class="mb-3">
                  <label class="form-label">Foto Bukti Pembayaran</label>
                  <input type="file" name="bukti_url" class="form-control" required>
               </div>

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
