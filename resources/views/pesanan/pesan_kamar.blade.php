@extends('layouts.app')

@section('content')
   <div class="container py-4">
      <!-- Header Section -->
      <div class="mb-4">
         <h1 class="h2 fw-bold text-dark mb-2">Daftar Pesanan Kamar</h1>
         <p class="text-muted">Kelola semua pesanan kamar kos</p>
      </div>

      <!-- Desktop Table View (hidden on mobile) -->
      <div class="d-none d-lg-block">
         <div class="card shadow-sm">
            <div class="table-responsive">
               <table class="table table-hover mb-0">
                  <thead class="table-primary">
                     <tr>
                        <th scope="col" class="text-center">No</th>
                        <th scope="col">Nama User</th>
                        <th scope="col">Nama Kos</th>
                        <th scope="col">Nama Kamar</th>
                        <th scope="col">Tanggal Pemesanan</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse ($pesanan as $index => $item)
                        <tr>
                           <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                           <td>{{ $item['nama'] }}</td>
                           <td>{{ $item['kos'] }}</td>
                           <td>{{ $item['kamar'] }}</td>
                           <td class="px-4 py-2 border">
                              {{ \Carbon\Carbon::parse($item['timestamp'])->format('Y-m-d') }}
                           </td>

                           <td class="text-center">
                              @if ($item['status'] === 'pending')
                                 <span class="badge bg-warning text-dark ">Pending</span>
                              @elseif($item['status'] === 'approved')
                                 <span class="badge bg-success">Approved</span>
                              @elseif($item['status'] === 'rejected')
                                 <span class="badge bg-danger">Rejected</span>
                              @elseif($item['status'] === 'completed')
                                 <span class="badge bg-info text-dark">Completed</span>
                              @else
                                 <span class="badge bg-secondary">{{ ucfirst($item['status']) }}</span>
                              @endif
                           </td>
                           <td class="text-center">
                              <div class="btn-group" role="group">
                                 <a href="{{ route('admin.pesanan.detail', $item['idDoc']) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Detail
                                 </a>
                                 <form action="{{ route('admin.pesanan.delete', $item['idDoc']) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin hapus pesanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                       <i class="fas fa-trash"></i> Hapus
                                    </button>
                                 </form>
                              </div>
                           </td>
                        </tr>
                     @empty
                        <tr>
                           <td colspan="7" class="text-center py-5">
                              <div class="d-flex flex-column align-items-center">
                                 <i class="fas fa-inbox fa-5x text-muted mb-3"></i>
                                 <h5 class="text-muted">Tidak ada data pesanan</h5>
                                 <p class="text-muted small">Pesanan akan muncul di sini setelah ada yang memesan</p>
                              </div>
                           </td>
                        </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      <!-- Mobile Card View (visible on mobile only) -->
      <div class="d-lg-none">
         @forelse ($pesanan as $index => $item)
            <div class="card shadow-sm mb-3">
               <!-- Card Header -->
               <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                  <span class="fw-semibold">Pesanan #{{ $index + 1 }}</span>
                  @if ($item['status'] === 'pending')
                     <span class="badge bg-warning text-dark">Pending</span>
                  @elseif($item['status'] === 'approved')
                     <span class="badge bg-success">Approved</span>
                  @elseif($item['status'] === 'rejected')
                     <span class="badge bg-danger">Rejected</span>
                  @elseif($item['status'] === 'completed')
                     <span class="badge bg-info text-dark">Completed</span>
                  @else
                     <span class="badge bg-secondary">{{ ucfirst($item['status']) }}</span>
                  @endif
               </div>

               <!-- Card Body -->
               <div class="card-body">
                  <div class="mb-3">
                     <div class="d-flex align-items-start">
                        <i class="fas fa-user text-primary me-3 fs-5"></i>
                        <div class="flex-grow-1">
                           <small class="text-muted d-block">Nama User</small>
                           <span class="fw-semibold">{{ $item['nama'] }}</span>
                        </div>
                     </div>
                  </div>

                  <div class="mb-3">
                     <div class="d-flex align-items-start">
                        <i class="fas fa-building text-primary me-3 fs-5"></i>
                        <div class="flex-grow-1">
                           <small class="text-muted d-block">Nama Kos</small>
                           <span class="fw-semibold">{{ $item['kos'] }}</span>
                        </div>
                     </div>
                  </div>

                  <div class="mb-3">
                     <div class="d-flex align-items-start">
                        <i class="fas fa-door-closed text-primary me-3 fs-5"></i>
                        <div class="flex-grow-1">
                           <small class="text-muted d-block">Nama Kamar</small>
                           <span class="fw-semibold">{{ $item['kamar'] }}</span>
                        </div>
                     </div>
                  </div>

                  <div class="mb-0">
                     <div class="d-flex align-items-start">
                        <i class="fas fa-calendar-alt text-primary me-3 fs-5"></i>
                        <div class="flex-grow-1">
                           <small class="text-muted d-block">Tanggal Pemesanan</small>
                           <span class="fw-semibold">{{ $item['timestamp'] }}</span>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Card Footer -->
               <div class="card-footer bg-light">
                  <div class="d-grid gap-2">
                     <a href="{{ route('admin.pesanan.detail', $item['idDoc']) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> Lihat Detail
                     </a>
                     <form action="{{ route('admin.pesanan.delete', $item['idDoc']) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin hapus pesanan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                           <i class="fas fa-trash"></i> Hapus Pesanan
                        </button>
                     </form>
                  </div>
               </div>
            </div>
         @empty
            <div class="card shadow-sm">
               <div class="card-body text-center py-5">
                  <i class="fas fa-inbox fa-5x text-muted mb-3"></i>
                  <h5 class="text-muted mb-2">Tidak ada data pesanan</h5>
                  <p class="text-muted small mb-0">Pesanan akan muncul di sini setelah ada yang memesan</p>
               </div>
            </div>
         @endforelse
      </div>
   </div>
@endsection
