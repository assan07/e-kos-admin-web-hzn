@extends('layouts.app')

@section('content')
   <div class="container mt-4">

      <div class="mb-4">
         <h2 class="fw-bold text-primary mb-1">
            <i class="bi bi-wallet2 me-2"></i>Detail Pembayaran
         </h2>
         <h4 class="text-muted">{{ $pembayaran['nama'] ?? '-' }}</h4>
      </div>

      <div class="card shadow-sm border-0 rounded-3">
         <div class="card-body p-4">

            {{-- Alert --}}
            @if (session('success'))
               <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button class="btn-close" data-bs-dismiss="alert"></button>
               </div>
            @endif

            <form action="{{ route('admin.pembayaran.update', $pembayaran['id_doc']) }}" method="POST">
               @csrf
               @method('PUT')

               <div class="row">
                  {{-- LEFT --}}
                  <div class="col-md-6">

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Nama User</label>
                        <input type="text" class="form-control" value="{{ $pembayaran['nama'] }}" readonly>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kos</label>
                        <input type="text" class="form-control" value="{{ $pembayaran['kos'] }}" readonly>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kamar</label>
                        <input type="text" class="form-control" value="{{ $pembayaran['kamar'] }}" readonly>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Bulan</label>
                        <input type="text" class="form-control" value="{{ $pembayaran['bulan'] }}" readonly>
                     </div>

                     <div class="mb-3">
                        <label class="form-label fw-semibold">Harga</label>
                        <input type="text" class="form-control"
                           value="Rp {{ number_format($pembayaran['harga'], 0, ',', '.') }}" readonly>
                     </div>

                     {{-- STATUS --}}
                     <div class="mb-3">
                        <label class="form-label fw-semibold">Status Pembayaran</label>
                        <select name="status_pembayaran" class="form-select form-select-lg" disabled>
                           <option value="sudah_bayar"
                              {{ $pembayaran['status_pembayaran'] == 'sudah_bayar' ? 'selected' : '' }}>🟢 Sudah Bayar
                           </option>

                           <option value="belum_bayar"
                              {{ $pembayaran['status_pembayaran'] == 'belum_bayar' ? 'selected' : '' }}>🟡 Belum Bayar
                           </option>

                           <option value="ditolak" {{ $pembayaran['status_pembayaran'] == 'ditolak' ? 'selected' : '' }}>
                              🔴 Ditolak</option>
                        </select>
                     </div>

                  </div>

                  {{-- RIGHT --}}
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label fw-semibold">Bukti Pembayaran</label>
                        <div class="border rounded-3 p-3 bg-light text-center" style="min-height:200px;">
                           @if ($pembayaran['bukti_url'])
                              <img src="{{ $pembayaran['bukti_url'] }}" class="img-fluid rounded-3 shadow-sm"
                                 style="max-height:300px; object-fit:cover;">
                           @else
                              <p class="text-muted">Tidak ada bukti pembayaran</p>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>

               {{-- BUTTONS --}}
               <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                  <a href="{{ url()->previous() }}" class="btn btn-secondary">
                     <i class="fas fa-arrow-left"></i> Kembali
                  </a>

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
