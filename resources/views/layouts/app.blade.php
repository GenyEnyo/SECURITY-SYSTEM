<!DOCTYPE html>
<html lang="en"
      data-bs-theme="light"
      data-skin="default"
      data-topbar-color="light"
      data-menu-color="dark"
      data-sidenav-size="default"
      data-layout-position="fixed"
      data-layout-width="fluid"
      dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'M Dashboard')</title>

  <link rel="shortcut icon" href="{{ asset('theme/images/favicon.ico') }}">

  <!-- Theme Config (must load early to avoid FOUC) -->
  <script src="{{ asset('theme/js/config.js') }}"></script>

  <!-- Vendor + App css -->
  <link href="{{ asset('theme/css/vendors.min.css') }}" rel="stylesheet" type="text/css">
  <link id="app-style" href="{{ asset('theme/css/app.min.css') }}" rel="stylesheet" type="text/css">

  @stack('head')
</head>

<body>
  <div class="wrapper">

    @include('partials.topbar')
    @include('partials.sidebar')

    <div class="content-page">
      <div class="container-fluid">

        <div class="page-title-head d-flex align-items-center">
          <div class="flex-grow-1">
            <h4 class="page-main-title m-0">@yield('page-title', 'M Dashboard')</h4>
          </div>
          <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
              @yield('crumbs')
            </ol>
          </div>
        </div>

        @yield('content')
      </div>

      @include('partials.footer')
    </div>
  </div>

  <script src="{{ asset('theme/js/vendors.min.js') }}"></script>
  <script src="{{ asset('theme/js/app.js') }}"></script>
  @stack('scripts')
</body>
</html>
