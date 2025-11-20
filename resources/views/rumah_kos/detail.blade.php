@extends('layouts.app')

@section('content')
   <div class="container py-4">
      <!-- Header Section -->
      <div class="d-flex justify-content-between align-items-center mb-4">
         <div>
            <h2 class="mb-1">{{ $kos['nama_kos'] }}</h2>
            <p class="text-muted mb-0">
               <i class="fas fa-map-marker-alt"></i> {{ $kos['lokasi'] }}
            </p>
         </div>
         @if ($kos['foto'])
            <img src="{{ $kos['foto'] }}" alt="Foto Kos" class="rounded shadow-sm"
               style="max-width: 150px; height: 100px; object-fit: cover;">
         @endif
      </div>

      <!-- Statistics Cards -->
      <div class="row g-3 mb-4">
         <!-- Total Kamar -->
         <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
               <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                     <div>
                        <p class="text-muted mb-1 small">Total Kamar</p>
                        <h3 class="mb-0 fw-bold" id="totalKamar">{{ $kos['jumlah_kamar'] }}</h3>
                     </div>
                     <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="fas fa-door-open text-primary fs-4"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Kamar Terisi -->
         <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
               <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                     <div>
                        <p class="text-muted mb-1 small">Kamar Terisi</p>
                        <h3 class="mb-0 fw-bold" id="kamarTerisi">0</h3>
                     </div>
                     <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="fas fa-home text-success fs-4"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Total Penghuni -->
         <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
               <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                     <div>
                        <p class="text-muted mb-1 small">Total Penghuni</p>
                        <h3 class="mb-0 fw-bold" id="totalPenghuni">0</h3>
                     </div>
                     <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="fas fa-users text-info fs-4"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Total Pembayaran -->
         <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
               <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                     <div>
                        <p class="text-muted mb-1 small">Total Pembayaran</p>
                        <h3 class="mb-0 fw-bold" id="totalPembayaran">Rp 0</h3>
                     </div>
                     <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="fas fa-wallet text-warning fs-4"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Tabel Kamar -->
      <div class="card border-0 shadow-sm mb-4">
         <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
               <h5 class="mb-0">
                  <i class="fas fa-bed me-2"></i>Data Kamar
               </h5>
               <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKamar">
                  <i class="fas fa-plus-circle me-1"></i>Tambah Kamar
               </button>
            </div>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-hover mb-0" id="tabelKamar">
                  <thead class="table-light">
                     <tr>
                        <th>ID Kamar</th>
                        <th>Nama Kamar</th>
                        <th>Jumlah Penghuni</th>
                        <th class="text-center">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td colspan="4" class="text-center py-4">
                           <div class="spinner-border spinner-border-sm text-primary" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                           <p class="text-muted mb-0 mt-2 small">Memuat data...</p>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      <!-- Tabel Riwayat Pembayaran -->
      <div class="card border-0 shadow-sm">
         <div class="card-header bg-white py-3">
            <h5 class="mb-0">
               <i class="fas fa-receipt me-2"></i>Riwayat Pembayaran
            </h5>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-hover mb-0" id="tabelPembayaran">
                  <thead class="table-light">
                     <tr>
                        <th>ID Pembayaran</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td colspan="4" class="text-center py-4">
                           <div class="spinner-border spinner-border-sm text-primary" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                           <p class="text-muted mb-0 mt-2 small">Memuat data...</p>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      <!-- Modal Tambah Kamar -->
      <!-- Modal Tambah Kamar -->
      <div class="modal fade" id="modalTambahKamar" tabindex="-1" aria-labelledby="modalTambahKamarLabel"
         aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="modalTambahKamarLabel"><i class="fas fa-plus-circle me-2"></i>Tambah Kamar
                     Baru</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
               </div>
               <div class="modal-body">
                  <form id="formTambahKamar" enctype="multipart/form-data">
                     @csrf
                     <input type="hidden" name="idDoc" value="{{ $kos['idDoc'] }}">
                     <input type="hidden" name="alamat" value="{{ $kos['lokasi'] ?? 'Alamat tidak tersedia' }}">

                     <div class="mb-3">
                        <label class="form-label">Nama Kamar</label>
                        <input type="text" class="form-control" name="nama_kamar" required>
                     </div>
                     <div class="mb-3">
                        <label class="form-label">No. Kamar / Kode</label>
                        <input type="text" class="form-control" name="no_kamar" required>
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Foto Kamar</label>
                        <input type="file" class="form-control" name="foto">
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Harga Sewa (Rp/bulan)</label>
                        <input type="number" class="form-control" name="harga" required>
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Fasilitas (pisah koma)</label>
                        <textarea class="form-control" name="fasilitas" rows="2"></textarea>
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                           <option value="tersedia">Tersedia</option>
                           <option value="terisi">Terisi</option>
                           <option value="maintenance">Maintenance</option>
                        </select>
                     </div>
                  </form>
               </div>
               <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                  <button type="button" class="btn btn-primary" id="btnSimpanKamar"><i
                        class="fas fa-save me-1"></i>Simpan</button>
               </div>
            </div>
         </div>
      </div>

   </div>
@endsection

@push('script')
   <script>
      $(document).ready(function() {
         let idDoc = '{{ $kos['idDoc'] }}';

         function showAlert(message, type = 'success') {
            $("#alertBox").html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
         }

         // Tambah kamar
         $('#btnSimpanKamar').click(function() {
            let form = $('#formTambahKamar')[0];
            let fd = new FormData(form);
            fd.append('idDoc', idDoc);
            fd.append('alamat', $("input[name='alamat']").val());

            let $btn = $(this);
            $btn.prop('disabled', true).html(
               '<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

            $.ajax({
               url: '/admin/rumah-kos/' + idDoc + '/kamar',
               method: 'POST',
               data: fd,
               processData: false,
               contentType: false,
               success: function(res) {
                  showAlert("Kamar berhasil ditambah", "success");
                  $('#modalTambahKamar').modal('hide');
                  location.reload();
               },
               error: function(xhr) {
                  let msg = 'Terjadi kesalahan';
                  if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                  showAlert(msg, "danger");
               },
               complete: function() {
                  $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan');
               }
            });
         });

         // Hapus kamar
         $('#tabelKamar').on('click', '.btn-hapus-kamar', function() {
            let idKamar = $(this).data('id-kamar');
            if (!confirm('Yakin ingin menghapus kamar ini?')) return;

            $.ajax({
               url: '/admin/rumah-kos/' + idDoc + '/kamar/' + idKamar,
               method: 'POST',
               data: {
                  _method: 'DELETE',
                  _token: '{{ csrf_token() }}'
               },
               success: function(res) {
                  alert(res.message || 'Kamar berhasil dihapus');
                  location.reload();
               },
               error: function(xhr) {
                  console.error(xhr.responseJSON || xhr.responseText);
                  alert('Gagal menghapus kamar, cek console untuk detail');
               }
            });
         });

         // Load tabel kamar
         $.getJSON('/admin/rumah-kos/' + idDoc + '/kamar', function(data) {
            let tbody = '';
            let totalPenghuni = 0;
            let kamarTerisi = 0;

            data.forEach(k => {
               let penghuni = k.jumlah_penghuni || 0;
               totalPenghuni += penghuni;
               if (penghuni > 0) kamarTerisi++;

               tbody += `<tr>
                <td><span class="badge bg-light text-dark">${k.id_kamar}</span></td>
                <td><strong>${k.nama_kamar}</strong></td>
                <td>${penghuni>0?`<span class="badge bg-success">${penghuni} Orang</span>`:`<span class="badge bg-secondary">Kosong</span>`}</td>
                <td class="text-center">
                    <a href="/admin/rumah-kos/${idDoc}/kamar/${k.id_kamar}/detail" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Detail</a>
                    <button class="btn btn-sm btn-outline-danger btn-hapus-kamar" data-id-kamar="${k.id_kamar}"><i class="fas fa-trash"></i> Hapus</button>
                </td>
            </tr>`;
            });

            $('#tabelKamar tbody').html(tbody);
            $('#totalPenghuni').text(totalPenghuni);
            $('#kamarTerisi').text(kamarTerisi);
         });

         // Load pembayaran
         $.getJSON('/admin/rumah-kos/' + idDoc + '/pembayaran', function(data) {
            let tbody = '';
            let totalAmount = 0;

            data.forEach(p => {
               totalAmount += parseFloat(p.amount) || 0;

               let statusBadge = '';
               switch (p.status.toLowerCase()) {
                  case 'lunas':
                  case 'paid':
                     statusBadge = `<span class="badge bg-success">${p.status}</span>`;
                     break;
                  case 'pending':
                     statusBadge = `<span class="badge bg-warning text-dark">${p.status}</span>`;
                     break;
                  default:
                     statusBadge = `<span class="badge bg-danger">${p.status}</span>`;
               }

               tbody += `<tr>
                <td><span class="badge bg-light text-dark">${p.id}</span></td>
                <td><strong>Rp ${parseInt(p.amount).toLocaleString('id-ID')}</strong></td>
                <td>${statusBadge}</td>
                <td class="text-center"><a href="#" class="btn btn-sm btn-outline-success"><i class="fas fa-file-invoice"></i> Lihat</a></td>
            </tr>`;
            });

            $('#tabelPembayaran tbody').html(tbody);
            $('#totalPembayaran').text('Rp ' + totalAmount.toLocaleString('id-ID'));
         });
      });
   </script>
@endpush
