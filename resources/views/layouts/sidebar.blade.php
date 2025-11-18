<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
   <div class="sidebar-logo">
      <!-- Logo Header -->
      <div class="logo-header" data-background-color="dark">
         <a href="index.html" class="logo">
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
      <!-- End Logo Header -->
   </div>
   <div class="sidebar-wrapper scrollbar scrollbar-inner">
      <div class="sidebar-content">
         <ul class="nav nav-secondary">
            <li class="nav-item active">
               <a data-bs-toggle="collapse" href="#dashboard" class="collapsed" aria-expanded="false">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
               </a>
            </li>

            <li class="nav-item">
               <a data-bs-toggle="collapse" href="#base">
                  <i class="fas fa-home"></i>
                  <p>Rumah Kos</p>
                  <span class="caret"></span>
               </a>
               <div class="collapse" id="base">
                  <ul class="nav nav-collapse">
                     <li>
                        <a href="#">
                           <span class="sub-item">Rumah Kos 1</span>
                        </a>
                     </li>
                  </ul>
               </div>
            </li>
            <li class="nav-item">
               <a data-bs-toggle="collapse" href="#sidebarLayouts">
                  <i class="fas fa-bed"></i>
                  <p>Kamar Kos</p>
                  <span class="caret"></span>
               </a>
               <div class="collapse" id="sidebarLayouts">
                  <ul class="nav nav-collapse">
                     <li>
                        <a href="#">
                           <span class="sub-item">Rumah Kos 1</span>
                        </a>
                     </li>
                  </ul>
               </div>
            </li>
            <li class="nav-item">
               <a data-bs-toggle="collapse" href="#forms">
                  <i class="fas fa-user-check"></i>
                  <p>Penghuni</p>
                  <span class="caret"></span>
               </a>
               <div class="collapse" id="forms">
                  <ul class="nav nav-collapse">
                     <li>
                        <a href="#">
                           <span class="sub-item">Rumah Kos 1</span>
                        </a>
                     </li>
                  </ul>
               </div>
            </li>
            <li class="nav-item">
               <a data-bs-toggle="collapse" href="#tables">
                  <i class="fas fa-money-check"></i>
                  <p>Pembayaran</p>
                  <span class="caret"></span>
               </a>
               <div class="collapse" id="tables">
                  <ul class="nav nav-collapse">
                     <li>
                        <a href="#">
                           <span class="sub-item">Rumah Kos 1</span>
                        </a>
                     </li>
                  </ul>
               </div>
            </li>
         </ul>
      </div>
   </div>
</div>
<!-- End Sidebar -->
