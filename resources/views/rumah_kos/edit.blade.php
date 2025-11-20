@extends('layouts.app')

@section('content')
   <div class="container mt-4">

      <div class="mb-4">
         <h2 class="fw-bold text-primary mb-1">
            <i class="fas fa-building me-2"></i>Edit Rumah Kos
         </h2>
         <h4 class="text-muted">{{ $kos['nama_kos'] ?? '-' }}</h4>
      </div>

      <div class="card shadow-sm border-0 rounded-3">
         <div class="card-body p-4">

            {{-- Alert --}}
            @if (session('success'))
               <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
               </div>
            @endif

            <form action="{{ route('rumah_kos.update', $kos['id']) }}" method="POST" enctype="multipart/form-data">
               @csrf
               @method('PUT')

               <div class="row">
                  {{-- Kolom Kiri --}}
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kos</label>
                        <input type="text" name="nama_kos" class="form-control" value="{{ $kos['nama_kos'] }}" required>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="{{ $kos['lokasi'] }}" required>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Kamar</label>
                        <input type="number" name="jumlah_kamar" class="form-control" value="{{ $kos['jumlah_kamar'] }}"
                           required min="1">
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Kos (Opsional)</label>
                        <input type="file" name="foto" class="form-control">
                     </div>
                  </div>

                  {{-- Kolom Kanan --}}
                  <div class="col-md-6">
                     <label class="form-label fw-semibold">Preview Foto</label>
                     <div class="border rounded-3 p-3 bg-light text-center" style="min-height:200px;">
                        @if ($kos['foto'])
                           <img src="{{ $kos['foto'] }}" class="img-fluid rounded-3 shadow-sm"
                              style="max-height:300px; object-fit:cover;">
                        @else
                           <p class="text-muted">Belum ada foto</p>
                        @endif
                     </div>
                  </div>
               </div>

               {{-- Tombol --}}
               <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                  <a href="{{ route('rumah_kos.index') }}" class="btn btn-secondary">
                     <i class="fas fa-arrow-left"></i> Kembali
                  </a>
                  <button type="submit" class="btn btn-primary btn-lg px-4">
                     <i class="fas fa-save me-2"></i>Simpan Perubahan
                  </button>
               </div>
            </form>

         </div>
      </div>
   </div>
@endsection
