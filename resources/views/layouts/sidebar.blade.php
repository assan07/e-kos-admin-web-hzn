<div class="sidebar" data-background-color="dark">
   <div class="sidebar-logo">
      <div class="logo-header" data-background-color="dark">
         <a href="{{ route('dashboard') }}" class="logo">
            <img src="{{ asset('assets/img/e-kos1.png') }}" alt="navbar brand" class="navbar-brand" height="20" />
         </a>
      </div>
   </div>
   <div class="sidebar-wrapper scrollbar scrollbar-inner">
      <div class="sidebar-content">
         <ul class="nav nav-secondary">
            <li class="nav-item active">
               <a href="{{ route('dashboard') }}">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
               </a>
            </li>

            <!-- Rumah Kos -->
            <li class="nav-item">
               <a data-bs-toggle="collapse" href="#rumahKosMenu" aria-expanded="false">
                  <i class="fas fa-home"></i>
                  <p>Rumah Kos</p>
                  <span class="caret"></span>
               </a>
               <div class="collapse" id="rumahKosMenu">
                  <ul class="nav nav-collapse">
                     @foreach ($kosList as $kos)
                        <li>
                           <a href="{{ route('rumah-kos.detail', $kos['id_kos']) }}">
                              <span class="sub-item">{{ $kos['nama_kos'] }}</span>
                           </a>
                        </li>
                     @endforeach

                  </ul>
               </div>
            </li>

            <!-- Pembayaran -->
            <li class="nav-item">
               <a data-bs-toggle="collapse" href="#pembayaranMenu" aria-expanded="false">
                  <i class="fas fa-money-check"></i>
                  <p>Pembayaran</p>
                  <span class="caret"></span>
               </a>
               <div class="collapse" id="pembayaranMenu">
                  <ul class="nav nav-collapse">
                     @foreach ($kosList as $kos)
                        <li>
                           <a href="#" class="pembayaran-link" data-id="{{ $kos['id_kos'] }}">
                              <span class="sub-item">{{ $kos['nama_kos'] }}</span>
                           </a>
                        </li>
                     @endforeach
                  </ul>
               </div>
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

         // Pembayaran link klik -> redirect/halaman pembayaran
         document.querySelectorAll('.pembayaran-link').forEach(function(el) {
            el.addEventListener('click', function(e) {
               e.preventDefault();
               const idKos = this.dataset.id;
               if (idKos === 'kos_placeholder') return; // Kos kosong, tidak redirect
               window.location.href = `/pembayaran/${idKos}`;
            });
         });

      });
   </script>
@endpush
