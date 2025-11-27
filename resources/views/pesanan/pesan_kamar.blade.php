@extends('layouts.app')
@section('title', 'Pesanan-Kamar')
@push('styles')
   <style>
      /* Card Hover Effects */
      .card-hover {
         transition: all 0.3s ease;
      }

      .card-hover:hover {
         transform: translateY(-5px);
         box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
      }

      .card-mobile-hover {
         transition: all 0.3s ease;
      }

      .card-mobile-hover:hover {
         transform: translateY(-3px);
         box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
      }

      /* Table Row Hover */
      .table-row-hover {
         transition: all 0.2s ease;
      }

      .table-row-hover:hover {
         background-color: rgba(102, 126, 234, 0.05) !important;
         transform: scale(1.01);
      }

      /* Avatar Circle */
      .avatar-circle {
         width: 35px;
         height: 35px;
         border-radius: 50%;
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         display: flex;
         align-items: center;
         justify-content: center;
         color: white;
         font-size: 0.85rem;
      }

      /* Info Icons */
      .info-icon {
         width: 40px;
         height: 40px;
         border-radius: 10px;
         display: flex;
         align-items: center;
         justify-content: center;
         color: white;
         flex-shrink: 0;
      }

      /* Empty State */
      .empty-state-icon {
         width: 120px;
         height: 120px;
         margin: 0 auto 1.5rem;
         border-radius: 50%;
         background: rgba(108, 117, 125, 0.1);
         display: flex;
         align-items: center;
         justify-content: center;
      }

      .empty-state-icon i {
         font-size: 3.5rem;
         color: #6c757d;
      }

      /* Button Group */
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

      /* Badge Styling */
      .badge {
         font-weight: 500;
         letter-spacing: 0.3px;
      }

      /* Smooth Animations */
      * {
         transition: all 0.2s ease;
      }
   </style>
@endpush

@section('content')
   <div class="container-fluid px-4 py-4">
      <!-- Header Section -->
      <div class="row mb-4">
         <div class="col-12">
            <div
               class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
               <div class="d-flex align-items-center">
                  <div class="bg-gradient rounded-3 p-3 me-3 shadow-sm"
                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                     <i class="fas fa-shopping-cart fa-2x"></i>
                  </div>
                  <div>
                     <h2 class="mb-0 fw-bold">Daftar Pesanan Kamar</h2>
                     <p class="text-muted mb-0 small">
                        <i class="fas fa-clock me-1"></i>Kelola dan pantau semua pesanan kamar kos
                     </p>
                  </div>
               </div>
               <div class="d-flex gap-2">
                  <button class="btn btn-outline-primary" onclick="location.reload()">
                     <i class="fas fa-sync-alt me-2"></i>Refresh
                  </button>

               </div>
            </div>
         </div>
      </div>

      <!-- Summary Cards -->
      <div class="row g-3 mb-4">
         <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div class="flex-shrink-0">
                        <div class="rounded-circle p-3" style="background: rgba(13, 110, 253, 0.1);">
                           <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                        </div>
                     </div>
                     <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 small fw-semibold">Total Pesanan</p>
                        <h3 class="mb-0 fw-bold text-primary">{{ $totalPesanan }}</h3>
                        <small class="text-success"><i class="fas fa-arrow-up me-1"></i>Semua data</small>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div class="flex-shrink-0">
                        <div class="rounded-circle p-3" style="background: rgba(255, 193, 7, 0.1);">
                           <i class="fas fa-hourglass-half fa-2x text-warning"></i>
                        </div>
                     </div>
                     <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 small fw-semibold">Diproses</p>
                        <h3 class="mb-0 fw-bold text-warning">{{ $totalDiproses }}</h3>
                        <small class="text-muted"><i class="fas fa-clock me-1"></i>Menunggu</small>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div class="flex-shrink-0">
                        <div class="rounded-circle p-3" style="background: rgba(25, 135, 84, 0.1);">
                           <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                     </div>
                     <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 small fw-semibold">Diterima</p>
                        <h3 class="mb-0 fw-bold text-success">{{ $totalDiterima }}</h3>
                        <small class="text-success"><i class="fas fa-check me-1"></i>Approved</small>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div class="flex-shrink-0">
                        <div class="rounded-circle p-3" style="background: rgba(220, 53, 69, 0.1);">
                           <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                     </div>
                     <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 small fw-semibold">Ditolak</p>
                        <h3 class="mb-0 fw-bold text-danger">{{ $totalDitolak }}</h3>
                        <small class="text-danger"><i class="fas fa-ban me-1"></i>Rejected</small>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Desktop Table View -->
      <div class="d-none d-lg-block">
         <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
               <div class="d-flex justify-content-between align-items-center">
                  <h5 class="mb-0 fw-semibold">
                     <i class="fas fa-table text-primary me-2"></i>Tabel Pesanan
                  </h5>
                  
               </div>
            </div>
            <div class="card-body p-0">
               <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                     <thead class="table-light">
                        <tr>
                           <th scope="col" class="text-center" style="width: 60px;">
                              No.
                           </th>
                           <th scope="col">
                              Nama User
                           </th>
                           <th scope="col">
                              Nama Kos
                           </th>
                           <th scope="col">
                              Nama Kamar
                           </th>
                           <th scope="col">
                              Tanggal Pemesanan
                           </th>
                           <th scope="col" class="text-center">
                              Status
                           </th>
                           <th scope="col" class="text-center" style="width: 180px;">
                              Aksi
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse ($pesanan as $index => $item)
                           <tr class="table-row-hover">
                              <td class="text-center">
                                 <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                              </td>
                              <td>
                                 <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                       <i class="fas fa-user"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $item['nama'] }}</span>
                                 </div>
                              </td>
                              <td>
                                 <div class="text-truncate" style="max-width: 200px;" title="{{ $item['kos'] }}">
                                    {{ $item['kos'] }}
                                 </div>
                              </td>
                              <td>
                                 <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                    <i class="fas fa-door-open me-1"></i>{{ $item['kamar'] }}
                                 </span>
                              </td>
                              <td>
                                 <div class="d-flex flex-column">
                                    <span
                                       class="small">{{ \Carbon\Carbon::parse($item['timestamp'])->format('d M Y') }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                       <i
                                          class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item['timestamp'])->format('H:i') }}
                                    </span>
                                 </div>
                              </td>
                              <td class="text-center">
                                 @if ($item['status'] === 'diproses')
                                    <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                       <i class="fas fa-spinner fa-spin me-1"></i>Diproses
                                    </span>
                                 @elseif($item['status'] === 'diterima')
                                    <span class="badge rounded-pill bg-success px-3 py-2">
                                       <i class="fas fa-check me-1"></i>Diterima
                                    </span>
                                 @elseif($item['status'] === 'ditolak')
                                    <span class="badge rounded-pill bg-danger px-3 py-2">
                                       <i class="fas fa-times me-1"></i>Ditolak
                                    </span>
                                 @else
                                    <span class="badge rounded-pill bg-secondary px-3 py-2">
                                       {{ ucfirst($item['status']) }}
                                    </span>
                                 @endif
                              </td>
                              <td class="text-center">
                                 <div class="btn-group" role="group">
                                    <a href="{{ route('admin.pesanan.detail', $item['idDoc']) }}"
                                       class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Lihat Detail">
                                       <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.pesanan.delete', $item['idDoc']) }}" method="POST"
                                       class="d-inline" onsubmit="return confirm('Yakin ingin hapus pesanan ini?')">
                                       @csrf
                                       @method('DELETE')
                                       <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                          title="Hapus Pesanan">
                                          <i class="fas fa-trash-alt"></i>
                                       </button>
                                    </form>
                                 </div>
                              </td>
                           </tr>
                        @empty
                           <tr>
                              <td colspan="7" class="text-center py-5">
                                 <div class="empty-state">
                                    <div class="empty-state-icon">
                                       <i class="fas fa-inbox"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">Tidak ada data pesanan</h5>
                                    <p class="text-muted small mb-0">Pesanan akan muncul di sini setelah ada yang memesan
                                    </p>
                                 </div>
                              </td>
                           </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>

   </div>

@endsection
@push('script')
   <script>
      // Initialize tooltips
      document.addEventListener('DOMContentLoaded', function() {
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
         var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
         });
      });
   </script>
@endpush
