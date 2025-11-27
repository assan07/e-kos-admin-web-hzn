@extends('layouts.app')
@section('title', 'Tambah Rumah Kos')

@section('content')
   <div class="container mt-4">
      <h3>Tambah Rumah Kos</h3>
      <div class="card mt-3">
         <div class="card-body">

            @if (session('success'))
               <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
               <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('rumah-kos.store') }}" method="POST" enctype="multipart/form-data">
               @csrf

               <div class="mb-3">
                  <label class="form-label">Nama Kos</label>
                  <input type="text" name="nama_kos" class="form-control" required>
                  @error('nama_kos')
                     <span class="text-danger">{{ $message }}</span>
                  @enderror
               </div>

               <div class="mb-3">
                  <label class="form-label">Lokasi</label>
                  <input type="text" name="lokasi" class="form-control" required>
                  @error('lokasi')
                     <span class="text-danger">{{ $message }}</span>
                  @enderror
               </div>

               <div class="mb-3">
                  <label class="form-label">Jumlah Kamar</label>
                  <input type="number" name="jumlah_kamar" class="form-control" min="1" required>
                  @error('jumlah_kamar')
                     <span class="text-danger">{{ $message }}</span>
                  @enderror
               </div>

               <div class="mb-3">
                  <label class="form-label">Foto Kos (URL)</label>
                  <input type="file" name="foto" class="form-control">
                  @error('foto')
                     <span class="text-danger">{{ $message }}</span>
                  @enderror
               </div>

               <button type="submit" class="btn btn-primary">Simpan</button>
               <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>

            </form>
         </div>
      </div>
   </div>
@endsection
