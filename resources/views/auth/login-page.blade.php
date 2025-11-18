<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>E-Kos</title>

   {{-- Fonts --}}
   <link rel="preconnect" href="https://fonts.bunny.net">
   <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

   {{-- Scripts --}}
   <link rel="icon" href="{{ asset('assets/img/e-kos1.png') }}" type="image/x-icon" />

   <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
      integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
      crossorigin="anonymous" referrerpolicy="no-referrer" />
   <script src="{{ asset('assets/js/login.js') }}"></script>

   {{-- CSS --}}
   <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

   <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

   {{-- Custom Styles --}}
</head>

<body>
   {{-- Animated background shapes --}}
   <div class="bg-shape bg-shape-1"></div>
   <div class="bg-shape bg-shape-2"></div>
   <div class="bg-shape bg-shape-3"></div>

   <div class="auth-container min-vh-100 d-flex align-items-center justify-content-center py-4">
      <div class="auth-card">
         <div class="row g-0">
            {{-- Left Side - Branding --}}
            <div class="col-lg-5 auth-left d-none d-lg-flex">
               <div class="auth-left-content">
                  <img src="{{ asset('assets/img/e-kos1.png') }}" alt="Logo" class="auth-logo">
                  <h2 class="auth-title">Selamat Datang Kembali!</h2>
                  <p class="auth-subtitle">
                     Masuk ke akun Anda untuk mengakses semua fitur dan layanan kami
                  </p>
                  {{-- Optional: Add illustration SVG here --}}
                  <svg class="auth-illustration" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                     <circle cx="250" cy="250" r="200" fill="rgba(255,255,255,0.1)" />
                     <circle cx="250" cy="250" r="150" fill="rgba(255,255,255,0.1)" />
                     <circle cx="250" cy="250" r="100" fill="rgba(255,255,255,0.1)" />
                  </svg>
               </div>
            </div>

            {{-- Right Side - Form --}}
            <div class="col-lg-7 auth-right">
               {{-- Mobile Logo --}}
               <div class="text-center mb-4 d-lg-none">
                  <img src="{{ asset('assets/img/e-kos1.png') }}" alt="Logo" style="max-height: 60px;">
               </div>

               <h3 class="form-title">Login</h3>
               <p class="form-subtitle">Masukkan kredensial Anda untuk melanjutkan</p>

               {{-- alert --}}
               @if ($errors->has('login'))
                  <div class="alert alert-danger d-flex align-items-center mb-3">
                     <i class="fas fa-exclamation-circle me-2"></i>
                     <span>{{ $errors->first('login') }}</span>
                  </div>
               @endif
               {{-- form login --}}
               <form method="POST" action="{{ route('login.process') }}">
               {{-- <form method="POST" action="#"> --}}
                  @csrf

                  {{-- Email Address --}}
                  <div class="mb-3">
                     <label for="email" class="form-label">
                        <i class="fas fa-envelope me-1"></i>
                        {{ __('Alamat Email') }}
                     </label>
                     <div class="input-group-icon">
                        <i class="fas fa-envelope"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="form-control " placeholder="nama@email.com" required autofocus
                           autocomplete="username" />
                     </div>

                  </div>

                  {{-- Password --}}
                  <div class="mb-3">
                     <label for="password" class="form-label">
                        <i class="fas fa-lock me-1"></i>
                        {{ __('Password') }}
                     </label>
                     <div class="input-group-icon position-relative">
                        <i class="fas fa-lock"></i>
                        <input id="password" type="password" name="password" class="form-control "
                           placeholder="Masukkan password Anda" required autocomplete="current-password" />
                        <button type="button"
                           class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted"
                           style="z-index: 10; text-decoration: none;" onclick="togglePassword()">
                           <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                     </div>

                  </div>

                  {{-- Submit Button --}}
                  <div class="mb-3">
                     <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        {{ __('Masuk') }}
                     </button>
                  </div>

               </form>
               {{-- end form Login --}}
            </div>
         </div>
      </div>
   </div>

   {{-- Scripts --}}
   <!--   Core JS Files   -->
   <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
   <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
   <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

   <!-- jQuery Scrollbar -->
   <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

   <!-- Chart JS -->
   <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

   <!-- jQuery Sparkline -->
   <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

   <!-- Chart Circle -->
   <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

   <!-- Datatables -->
   <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

   <!-- Bootstrap Notify -->
   <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

   <!-- jQuery Vector Maps -->
   <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
   <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>

   <!-- Sweet Alert -->
   <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

   <!-- Kaiadmin JS -->
   <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

</body>

</html>
