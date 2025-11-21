<div class="sidebar" data-background-color="dark">
   <div class="sidebar-logo">
      <div class="logo-header" data-background-color="dark">
         <a href="{{ route('dashboard') }}" class="logo">
            <img src="{{ asset('assets/img/e-kos1.png') }}" alt="navbar brand" class="navbar-brand" height="20" />
         </a>
         <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
               <i class="gg-menu-right"></i>
            </button>
            <button class="btn btn-toggle sidenav-toggler">
               <i class="gg-menu-left"></i>
            </button>
         </div>
         <button class="topbar-toggler more">
            <i class="gg-more-vertical-alt"></i>
         </button>
      </div>
   </div>
   <div class="sidebar-wrapper scrollbar scrollbar-inner">
      <div class="sidebar-content">
         <ul class="nav nav-secondary">
            {{-- Dashboard --}}
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
               <a href="{{ route('dashboard') }}">
                  <i class="fa fa-dashboard" aria-hidden="true"></i>
                  <p>Dashboard</p>
               </a>
            </li>

            {{-- Rumah Kos --}}
            <li class="nav-item {{ request()->routeIs('rumah_kos.*') ? 'active' : '' }}">
               <a href="{{ route('rumah_kos.index') }}">
                  <i class="fas fa-home"></i>
                  <p>Rumah Kos</p>
               </a>
            </li>

            {{-- Kamar Kos --}}
            @php
               $isKamarActive = request()->routeIs('kamar.*') || request()->routeIs('rumah-kos.detail');
            @endphp
            <li class="nav-item {{ $isKamarActive ? 'active' : '' }}">
               <a data-bs-toggle="collapse" href="#rumahKosMenu"
                  aria-expanded="{{ $isKamarActive ? 'true' : 'false' }}">
                  <i class="fas fa-bed"></i>
                  <p>Kamar Kos</p>
                  <span class="caret"></span>
               </a>
               <div class="collapse {{ $isKamarActive ? 'show' : '' }}" id="rumahKosMenu">
                  <ul class="nav nav-collapse">
                     @foreach ($kosList as $kos)
                        <li
                           class="{{ request()->url() === route('rumah-kos.detail', $kos['id_kos']) ? 'active' : '' }}">
                           <a href="{{ route('rumah-kos.detail', $kos['id_kos']) }}">
                              <span class="sub-item">{{ $kos['nama_kos'] }}</span>
                           </a>
                        </li>
                     @endforeach
                  </ul>
               </div>
            </li>

            {{-- Pesanan --}}
            <li class="nav-item {{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">
               <a href="{{ route('admin.pesanan.index') }}">
                  <i class="fas fa-envelope"></i>
                  <p>Pesanan</p>
               </a>
            </li>

            {{-- Pembayaran --}}
            <li class="nav-item {{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}">
               <a href="{{ route('admin.pembayaran.index') }}">
                  <i class="fas fa-money-check"></i>
                  <p>Pembayaran</p>
               </a>
            </li>
         </ul>
      </div>
   </div>

</div>
@push('script')
   <script>
      document.addEventListener('DOMContentLoaded', function() {

         // Rumah Kos link klik -> redirect/halaman kos
         const links = document.querySelectorAll('.kos-link');
         links.forEach(link => {
            link.addEventListener('click', function(e) {
               e.preventDefault();
               const idKos = this.dataset.id;

               fetch(`/admin/sidebar/kamar/${idKos}`)
                  .then(res => res.json())
                  .then(data => {
                     console.log('Kamar kos:', data);
                     // TODO: tampilkan kamar di modal atau div khusus
                  })
                  .catch(err => console.error(err));
            });
         });



      });
   </script>
@endpush
