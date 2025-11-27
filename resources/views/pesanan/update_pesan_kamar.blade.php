@extends('layouts.app')

@section('title', 'Detail Pesanan Kamar')

@section('content')
   <div class="container mt-4">

      <div class="mb-4">
         <h2 class="fw-bold text-primary mb-1">
            <i class="bi bi-receipt me-2"></i>Detail Pesanan
         </h2>
         <h4 class="text-muted">{{ $pesanan['nama'] ?? '-' }}</h4>
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

            <form action="{{ route('admin.pesanan.update', $pesanan['idDoc']) }}" method="POST">
               @csrf
               @method('PUT')

               <div class="row">
                  {{-- Kolom Kiri --}}
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label fw-semibold">Nama User</label>
                        <input type="text" class="form-control" value="{{ $pesanan['nama'] }}" readonly>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kos</label>
                        <input type="text" class="form-control" value="{{ $pesanan['kos'] }}" readonly>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kamar</label>
                        <input type="text" class="form-control" value="{{ $pesanan['kamar'] }}" readonly>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Harga</label>
                        <input type="text" class="form-control"
                           value="Rp {{ number_format($pesanan['harga'], 0, ',', '.') }}" readonly>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">No HP</label>
                        <input type="text" class="form-control" value="{{ $pesanan['no_hp'] }}" readonly>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Pemesanan</label>
                        <input type="text" class="form-control" value="{{ $pesanan['timestamp'] ?? '' }}" readonly>
                     </div>

                     {{-- Status --}}
                     <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select form-select-lg" disabled>
                           <option value="diproses" {{ $pesanan['status'] == 'diproses' ? 'selected' : '' }}>🟡 Diproses
                           </option>
                           <option value="diterima" {{ $pesanan['status'] == 'diterima' ? 'selected' : '' }}>🟢
                              Diterima
                           </option>
                           <option value="ditolak" {{ $pesanan['status'] == 'ditolak' ? 'selected' : '' }}>🔴 Ditolak
                           </option>
                        </select>
                     </div>
                  </div>

                  {{-- Kolom Kanan --}}
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label fw-semibold">Foto KTP</label>
                        <div class="border rounded-3 p-3 bg-light text-center" style="min-height:200px;">
                           @if ($pesanan['foto_ktp'])
                              <img src="{{ $pesanan['foto_ktp'] }}" class="img-fluid rounded-3 shadow-sm"
                                 style="max-height:300px; object-fit:cover;">
                           @else
                              <p class="text-muted">Tidak ada foto KTP</p>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>

               {{-- Tombol --}}
               <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                  <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                     Kembali</a>
                  <div class="d-flex gap-2">
                     <button type="button" id="btnEdit" class="btn btn-warning btn-lg px-4">Edit</button>
                     <button type="submit" id="btnSimpan" class="btn btn-primary btn-lg px-4 d-none">Simpan</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
@endsection

@push('script')
   <script>
      const btnEdit = document.getElementById('btnEdit');
      const btnSimpan = document.getElementById('btnSimpan');

      btnEdit.addEventListener('click', function() {
         document.querySelectorAll('select').forEach(el => el.removeAttribute('disabled'));
         btnEdit.classList.add('d-none');
         btnSimpan.classList.remove('d-none');
      });
   </script>
@endpush
