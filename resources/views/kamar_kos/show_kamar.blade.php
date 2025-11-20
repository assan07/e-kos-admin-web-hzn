@extends('layouts.app')

@section('content')
   <div class="container mt-4">

      {{-- Header dengan Gradient --}}
      <div class="mb-4">
         <h2 class="fw-bold text-primary mb-1">
            <i class="bi bi-door-open me-2"></i>Detail Kamar
         </h2>
         <h4 class="text-muted">{{ $kamar->nama_kamar ?? '-' }}</h4>
      </div>

      <div class="card shadow-sm border-0 rounded-3">
         <div class="card-body p-4">

            {{-- Alert --}}
            @if (session('success'))
               <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
               </div>
            @endif

            @if (session('error'))
               <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
               </div>
            @endif

            {{-- Form Detail + Edit --}}
            <form id="kamarForm" action="{{ route('kamar.update', ['idDoc' => $idDoc, 'idKamar' => $kamar->id]) }}"
               method="POST" enctype="multipart/form-data">

               @csrf
               @method('PUT')

               <div class="row">
                  {{-- Kolom Kiri --}}
                  <div class="col-md-6">
                     {{-- Nama Kamar --}}
                     <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">
                           <i class="bi bi-tag me-1"></i>Nama Kamar
                        </label>
                        <input type="text" name="nama_kamar" class="form-control form-control-lg"
                           value="{{ old('nama_kamar', $kamar->nama_kamar) }}" readonly>
                        @error('nama_kamar')
                           <span class="text-danger small mt-1 d-block"><i
                                 class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                        @enderror
                     </div>

                     {{-- No Kamar --}}
                     <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">
                           <i class="bi bi-hash me-1"></i>No. Kamar
                        </label>
                        <input type="text" name="no_kamar" class="form-control form-control-lg"
                           value="{{ old('no_kamar', $kamar->no_kamar) }}" readonly>
                        @error('no_kamar')
                           <span class="text-danger small mt-1 d-block"><i
                                 class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                        @enderror
                     </div>

                                          {{-- Harga Sewa --}}
                     <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">
                           <i class="bi bi-cash-coin me-1"></i>Harga Sewa (per bulan)
                        </label>
                        <div class="input-group input-group-lg">
                           <span class="input-group-text bg-light">Rp</span>
                           <input type="number" name="harga_sewa" class="form-control"
                              value="{{ old('harga_sewa', $kamar->harga_sewa) }}" readonly>
                        </div>
                        @error('harga_sewa')
                           <span class="text-danger small mt-1 d-block"><i
                                 class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                        @enderror
                     </div>

                     {{-- Status --}}
                     <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">
                           <i class="bi bi-info-circle me-1"></i>Status
                        </label>
                        <select name="status" class="form-select form-select-lg" disabled>
                           <option value="tersedia" {{ $kamar->status == 'tersedia' ? 'selected' : '' }}>
                              🟢 Tersedia
                           </option>
                           <option value="terisi" {{ $kamar->status == 'terisi' ? 'selected' : '' }}>
                              🔴 Terisi
                           </option>
                           <option value="maintenance" {{ $kamar->status == 'maintenance' ? 'selected' : '' }}>
                              🟡 Maintenance
                           </option>
                        </select>
                     </div>
                  </div>

                  {{-- Kolom Kanan --}}
                  <div class="col-md-6">
                     {{-- Foto --}}
                     <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary d-block">
                           <i class="bi bi-image me-1"></i>Foto Kamar
                        </label>

                        <div class="border rounded-3 p-3 bg-light text-center" style="min-height: 200px;">
                           @if ($kamar->foto_url)
                              <img src="{{ $kamar->foto_url }}" alt="foto kamar"
                                 class="img-fluid rounded-3 shadow-sm mb-2" style="max-height: 300px; object-fit: cover;">
                           @else
                              <div class="d-flex align-items-center justify-content-center" style="height: 200px;">
                                 <div class="text-muted">
                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Tidak ada foto</p>
                                 </div>
                              </div>
                           @endif
                        </div>

                        <input type="file" name="foto_kamar" class="form-control mt-2 d-none" id="fotoInput"
                           accept="image/*">
                        @error('foto_kamar')
                           <span class="text-danger small mt-1 d-block"><i
                                 class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                        @enderror
                     </div>

                     {{-- Fasilitas --}}
                     <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">
                           <i class="bi bi-star me-1"></i>Fasilitas
                        </label>
                        <textarea name="fasilitas" rows="5" class="form-control" style="resize: none;" readonly>{{ old('fasilitas', $kamar->fasilitas) }}</textarea>
                        @error('fasilitas')
                           <span class="text-danger small mt-1 d-block"><i
                                 class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                        @enderror
                     </div>
                  </div>
               </div>

               {{-- Tombol Aksi --}}
               <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-lg px-4">
                     <i class="bi bi-arrow-left me-2"></i>Kembali
                  </a>

                  <div class="d-flex gap-2">
                     <button type="button" id="btnEdit" class="btn btn-warning btn-lg px-4">
                        <i class="bi bi-pencil me-2"></i>Edit
                     </button>

                     <button type="submit" id="btnSimpan" class="btn btn-primary btn-lg px-4 d-none">
                        <i class="bi bi-save me-2"></i>Simpan
                     </button>
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
         // enable input
         document.querySelectorAll('input, textarea, select').forEach(el => {
            if (el.type !== 'file') el.removeAttribute('readonly');
            el.removeAttribute('disabled');
         });

         document.getElementById('fotoInput').classList.remove('d-none');

         btnEdit.classList.add('d-none');
         btnSimpan.classList.remove('d-none');
      });
   </script>
@endpush

@push('styles')
   <style>
      .form-control:read-only,
      .form-select:disabled {
         background-color: #f8f9fa;
         cursor: not-allowed;
      }

      .form-control:focus,
      .form-select:focus {
         border-color: #0d6efd;
         box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
      }

      .card {
         transition: transform 0.2s ease;
      }

      .btn {
         transition: all 0.3s ease;
      }

      .btn:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      }

      .img-fluid {
         transition: transform 0.3s ease;
      }

      .img-fluid:hover {
         transform: scale(1.02);
      }
   </style>
@endpush
