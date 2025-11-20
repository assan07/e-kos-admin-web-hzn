@push('styles')
   <style>
      .table-hover tbody tr:hover {
         background-color: rgba(0, 123, 255, 0.05);
         cursor: pointer;
      }

      .btn-group .btn {
         border-radius: 0;
      }

      .btn-group .btn:first-child {
         border-top-left-radius: 0.25rem;
         border-bottom-left-radius: 0.25rem;
      }

      .btn-group .btn:last-child {
         border-top-right-radius: 0.25rem;
         border-bottom-right-radius: 0.25rem;
      }
   </style>
@endpush

@extends('layouts.app')
@section('content')
   <div class="container-fluid px-4 py-4">
      {{-- Header Section --}}
      <div class="row mb-4">
         <div class="col-12">
            <div
               class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
               <div class="d-flex align-items-center">
                  <div class="bg-primary bg-gradient rounded-3 p-3 me-3 shadow-sm">
                     <i class="fas fa-building text-white fa-2x"></i>
                  </div>
                  <div>
                     <h2 class="mb-0 fw-bold">Data Rumah Kos</h2>
                     <p class="text-muted mb-0 small">Kelola semua data rumah kos Anda</p>
                  </div>
               </div>
               {{-- <a href="{{ route('rumah_kos.create') }}" class="btn btn-primary btn-lg shadow-sm">
                  <i class="fas fa-plus-circle me-2"></i>Tambah Kos Baru
               </a> --}}
            </div>
         </div>
      </div>

      {{-- Desktop Table View --}}
      <div class="card shadow-sm border-0 d-none d-lg-block">
         <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">
               <i class="fas fa-list text-primary me-2"></i>Daftar Rumah Kos
            </h5>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                     <tr>
                        <th class="text-center" style="width: 100px;">Foto</th>
                        <th>Nama Kos</th>
                        <th><i class="fas fa-map-marker-alt text-danger me-1"></i>Lokasi</th>
                        <th class="text-center"><i class="fas fa-door-open me-1"></i>Kamar</th>
                        <th class="text-center">Dibuat</th>
                        <th class="text-center">Diperbarui</th>
                        <th class="text-center" style="width: 180px;">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($rumahKos as $kos)
                        <tr>
                           <td class="text-center">
                              <img src="{{ $kos['foto'] }}" alt="{{ $kos['nama_kos'] }}" class="rounded shadow-sm"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                           </td>
                           <td>
                              <div class="fw-semibold">{{ $kos['nama_kos'] }}</div>
                           </td>
                           <td>
                              <span class="text-muted">
                                 <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                 {{ $kos['lokasi'] }}
                              </span>
                           </td>
                           <td class="text-center">
                              <span class="badge bg-info rounded-pill px-3 py-2">
                                 {{ $kos['jumlah_kamar'] }} Kamar
                              </span>
                           </td>
                           <td class="text-center text-muted small">
                              <div><i
                                    class="far fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($kos['created_at'])->format('d M Y') }}
                              </div>
                              <div class="text-muted" style="font-size: 0.75rem;">
                                 {{ \Carbon\Carbon::parse($kos['created_at'])->format('H:i') }}</div>
                           </td>
                           <td class="text-center text-muted small">
                              <div><i
                                    class="far fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($kos['updated_at'])->format('d M Y') }}
                              </div>
                              <div class="text-muted" style="font-size: 0.75rem;">
                                 {{ \Carbon\Carbon::parse($kos['updated_at'])->format('H:i') }}</div>
                           </td>
                           <td class="text-center">
                              <div class="btn-group d-flex gap-2" role="group" >
                                 <a href="{{ route('rumah_kos.edit', $kos['id']) }}" class="btn btn-sm btn-warning"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                 </a>
                                 <form action="{{ route('rumah_kos.destroy', $kos['id']) }}" method="POST"
                                    class="d-inline-block"
                                    onsubmit="return confirm('Yakin ingin menghapus kos {{ $kos['nama_kos'] }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                       <i class="fas fa-trash-alt"></i>
                                    </button>
                                 </form>
                              </div>
                           </td>
                        </tr>
                     @empty
                        <tr>
                           <td colspan="7" class="text-center py-5">
                              <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                              <p class="text-muted mb-0">Belum ada data rumah kos</p>
                           </td>
                        </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
@endsection
