@extends('layouts.app')

@section('content')
   <div class="page-inner">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
         <div>
            <h3 class="fw-bold mb-3">Dashboard</h3>
         </div>
         <div class="ms-md-auto py-2 py-md-0">
            <a href="#" class="btn btn-label-info btn-round me-2">Manage</a>
            <a href="{{ route('rumah-kos.create') }}" class="btn btn-primary btn-round">
               <span class="fas fa-plus"></span> Rumah Kos
            </a>
         </div>

      </div>
      {{-- Data Kos  --}}
      <div class="row">
         <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                           <i class="fas fa-home"></i>
                        </div>
                     </div>
                     <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                           <p class="card-category">Rumah Kos</p>
                           <h4 class="card-title" id="statRumahKos">2</h4>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                           <i class="fas fa-bed"></i>
                        </div>
                     </div>
                     <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                           <p class="card-category">Jumlah Kamar</p>
                           <h4 class="card-title" id="statKamar">40</h4>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                           <i class="fas fa-circle-notch"></i>
                        </div>
                     </div>
                     <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                           <p class="card-category">Kamar Terisi</p>
                           <h4 class="card-title" id="statKamarTerisi">20</h4>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                           <i class="fas fa-user-check"></i>
                        </div>
                     </div>
                     <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                           <p class="card-category">Penghuni</p>
                           <h4 class="card-title" id="statPenghuni">33</h4>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      {{-- end  Data Kos  --}}
      {{-- New Residents --}}
      <div class="row">
         <div class="col-md-4">
            <div class="card card-round">
               <div class="card-body">
                  <div class="card-head-row card-tools-still-right">
                     <div class="card-title">Penghuni Baru</div>
                  </div>
                  <div class="card-list py-4">
                     <div class="item-list">
                        <div class="avatar">
                           <img src="{{ asset('assets/img/jm_denis.jpg') }}" alt="..."
                              class="avatar-img rounded-circle" />
                        </div>
                        <div class="info-user ms-3">
                           <div class="username">Jimmy Denis</div>
                           <div class="status">Graphic Designer</div>
                        </div>
                        <button class="btn btn-icon btn-link op-8 me-1">
                           <i class="far fa-envelope"></i>
                        </button>
                        <button class="btn btn-icon btn-link btn-danger op-8">
                           <i class="fas fa-ban"></i>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         {{-- end New Residents --}}
         {{-- payment history  --}}
         <div class="col-md-8">
            <div class="card card-round">
               <div class="card-header">
                  <div class="card-head-row card-tools-still-right">
                     <div class="card-title">Riwayat Pembayaran</div>

                  </div>
                  <div class="card-body p-0">
                     <div class="table-responsive">
                        <!-- Projects table -->
                        <table class="table align-items-center mb-0">
                           <thead class="thead-light">
                              <tr>
                                 <th scope="col">Payment Number</th>
                                 <th scope="col" class="text-end">Date & Time</th>
                                 <th scope="col" class="text-end">Amount</th>
                                 <th scope="col" class="text-end">Status</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <th scope="row">
                                    <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                                       <i class="fa fa-check"></i>
                                    </button>
                                    Payment from #10231
                                 </th>
                                 <td class="text-end">Mar 19, 2020, 2.45pm</td>
                                 <td class="text-end">$250.00</td>
                                 <td class="text-end">
                                    <span class="badge badge-success">Completed</span>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         {{-- end payment history  --}}
      </div>
   @endsection
