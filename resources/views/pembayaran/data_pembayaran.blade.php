@extends('layouts.app')

@section('content')
   <div class="container mt-4">

      {{-- Header Section --}}
      <div class="d-flex justify-content-between align-items-center mb-4">
         <div>
            <h2 class="fw-bold mb-1">
               <i class="fas fa-receipt text-primary me-2"></i>
               Riwayat Pembayarand
            </h2>
            <p class="text-muted mb-0">Kelola dan pantau pembayaran kos Anda</p>
         </div>
         <div>
            {{-- Filter Kos --}}
            <select id="filterKos" class="form-select form-select-sm">
               <option value="">-- Semua Kos --</option>
               @foreach ($allKos as $kos)
                  <option value="{{ $kos }}">{{ $kos }}</option>
               @endforeach
            </select>
         </div>
      </div>

      {{-- Info Cards --}}
      <div class="row g-3 mb-4">
         <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                        <i class="fas fa-home fa-2x text-white"></i>
                     </div>
                     <div>
                        <p class="text-muted mb-1 small">Kos & Kamar</p>
                        <h5 class="mb-0 fw-bold" id="cardKosName">Semua Kos</h5>
                        <p class="mb-0 text-primary" id="cardKamarName">Semua Kamar</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                        <i class="fas fa-check-circle fa-2x text-white"></i>
                     </div>
                     <div>
                        <p class="text-muted mb-1 small">Status Pembayaran</p>
                        <h5 class="mb-0 fw-bold">
                           <span class="text-success" id="cardJumlahBayar">{{ $jumlah_bayar }}</span> /
                           <span class="text-warning" id="cardJumlahBelum">{{ $jumlah_belum }}</span>
                        </h5>
                        <p class="mb-0 small text-muted">Terbayar / Belum Bayar</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-card bg-gradient-primary text-white">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3 me-3">
                        <i class="fas fa-wallet fa-2x"></i>
                     </div>
                     <div>
                        <p class="mb-1 small opacity-75">Total Pemasukan</p>
                        <h4 class="mb-0 fw-bold" id="cardTotalPemasukan">Rp
                           {{ number_format($total_pemasukan, 0, ',', '.') }}
                        </h4>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      {{-- Tabel Riwayat Pembayaran --}}
      <div class="card border-0 shadow-sm">
         <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
               <i class="fas fa-list-alt text-primary me-2"></i>
               Daftar Pembayaran
            </h5>
            <div class="d-flex gap-2">
               <a href="{{ route('admin.pembayaran.addForm') }}" class="btn btn-sm btn-outline-info">
                  <i class="fas fa-plus me-1"></i> Tambah Pembayaran
               </a>
               <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#downloadModal">
                  <i class="fas fa-download me-1"></i> Download
               </button>
            </div>
         </div>

         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-hover align-middle mb-0" id="tablePayments">
                  <thead class="bg-light">
                     <tr>
                        <th class="py-3 ps-4">No</th>
                        <th class="py-3">Nama</th>
                        <th class="py-3">Kamar</th>
                        <th class="py-3">Periode</th>
                        <th class="py-3">Harga</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 pe-4">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($payments as $i => $p)
                        <tr data-kos="{{ $p['kos'] }}">
                           <td class="ps-4"><span class="badge bg-light text-dark">{{ $i + 1 }}</span></td>
                           <td>{{ $p['nama'] }}</td>
                           <td>{{ $p['kamar'] }}</td>
                           <td>{{ $p['bulan'] }}</td>
                           <td>Rp {{ number_format($p['harga'], 0, ',', '.') }}</td>
                           <td class="text-center">
                              @if ($p['status_pembayaran'] === 'sudah_bayar')
                                 <span class="badge bg-success">Sudah Bayar</span>
                              @elseif($p['status_pembayaran'] === 'belum_bayar')
                                 <span class="badge bg-warning text-dark">Belum Bayar</span>
                              @elseif($p['status_pembayaran'] === 'ditolak')
                                 <span class="badge bg-danger">Ditolak</span>
                              @else
                                 <span class="badge bg-secondary">{{ ucfirst($p['status_pembayaran']) }}</span>
                              @endif
                           </td>

                           <td class="text-center">
                              <div class="btn-group" role="group">
                                 <a href="{{ route('admin.pembayaran.detail', $p['id_pesanan']) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Detail
                                 </a>

                                 <form action="{{ route('admin.pembayaran.delete', $p['id_pesanan']) }}" method="POST"
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
                                 <h5 class="text-muted">Tidak ada data pembayaran !</h5>
                                 <p class="text-muted small">Pembayaran akan muncul di sini setelah ada yang membayar</p>
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

   {{-- Modal Pilih Kos untuk Download --}}
   <div class="modal fade" id="downloadModal" tabindex="-1">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Pilih Kos untuk Download</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
               <select id="downloadKosSelect" class="form-select">
                  @foreach ($allKos as $kos)
                     <option value="{{ $kos }}">{{ $kos }}</option>
                  @endforeach
               </select>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-primary" id="btnDownload">Download</button>
            </div>
         </div>
      </div>
   </div>
@endsection
@push('script')
   <script>
      // Filter table berdasarkan kos
      const filterSelect = document.getElementById('filterKos');
      const tableRows = document.querySelectorAll('#tablePayments tbody tr');

      filterSelect.addEventListener('change', () => {
         const filter = filterSelect.value;
         tableRows.forEach(row => {
            if (!filter || row.dataset.kos === filter) {
               row.style.display = '';
            } else {
               row.style.display = 'none';
            }
         });
      });

      // Tombol download (modal)
      document.getElementById('btnDownload').addEventListener('click', () => {
         const kos = document.getElementById('downloadKosSelect').value.trim();
         if (!kos) {
            alert('Pilih kos terlebih dahulu!');
            return;
         }
         // Pastikan encodeURIComponent untuk keamanan URL
         window.location.href = `/admin/pembayaran/download/${encodeURIComponent(kos)}`;
      });
   </script>
@endpush
@push('styles')
   <style>
      .hover-card {
         transition: all 0.3s ease;
      }

      .hover-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
      }

      .icon-box {
         display: inline-flex;
         align-items: center;
         justify-content: center;
         width: 60px;
         height: 60px;
      }

      .bg-gradient-primary {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      }

      .avatar-circle {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         display: inline-flex;
         align-items: center;
         justify-content: center;
         font-weight: bold;
         font-size: 16px;
      }

      .table> :not(caption)>*>* {
         padding: 1rem 0.75rem;
      }

      .badge {
         font-weight: 500;
         padding: 0.5em 0.85em;
      }

      .img-thumbnail {
         border: 2px solid #e9ecef;
         transition: all 0.3s ease;
      }

      .img-thumbnail:hover {
         transform: scale(1.05);
         border-color: #667eea;
      }

      .modal-content {
         border-radius: 1rem;
         overflow: hidden;
      }

      .form-select {
         border-radius: 0.5rem;
         border: 2px solid #e9ecef;
         padding: 0.75rem 1rem;
      }

      .form-select:focus {
         border-color: #667eea;
         box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
      }

      .btn {
         border-radius: 0.5rem;
         padding: 0.5rem 1rem;
         font-weight: 500;
         transition: all 0.3s ease;
      }

      .btn-sm {
         padding: 0.4rem 0.8rem;
      }

      .btn:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }
   </style>
@endpush
