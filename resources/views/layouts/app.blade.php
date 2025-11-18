<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   {{-- intall firebase --}}
   {{-- @vite(['resources/css/app.css','resources/js/app.js']) --}}
   <link rel="icon" href="{{ asset('assets/img/e-kos1.png') }}" type="image/x-icon" />

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
      integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
      crossorigin="anonymous" referrerpolicy="no-referrer" />

   <!-- Fonts and icons -->
   <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>

   <script>
      WebFont.load({
         google: {
            families: ["Public Sans:300,400,500,600,700"]
         },
         custom: {
            families: [
               "Font Awesome 5 Solid",
               "Font Awesome 5 Regular",
               "Font Awesome 5 Brands",
               "simple-line-icons",
            ],
         },
         active: function() {
            sessionStorage.fonts = true;
         },
      });
   </script>

   <!-- CSS Files -->
   <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" /> 


   <title>E kos </title>
</head>

<body>
   <div class="wrapper">
      @include('layouts.sidebar')
      <div class="main-panel">
         @include('layouts.header')

         <div class="container">
            @yield('content')
         </div>

         @include('layouts.footer')
      </div>
   </div>
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
