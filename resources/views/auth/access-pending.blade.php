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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Access pending · M Dashboard</title>

  <link rel="shortcut icon" href="{{ asset('theme/images/favicon.ico') }}">

  <!-- Theme Config (must load early to avoid FOUC) -->
  <script src="{{ asset('theme/js/config.js') }}"></script>

  <!-- Vendor + App css -->
  <link href="{{ asset('theme/css/vendors.min.css') }}" rel="stylesheet" type="text/css">
  <link id="app-style" href="{{ asset('theme/css/app.min.css') }}" rel="stylesheet" type="text/css">
</head>

<body>

  <div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="card shadow-sm w-100" style="max-width:460px;">
      <div class="card-body p-4 text-center">
        <span class="avatar-md bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3 fs-24">
          <i class="ti ti-lock"></i>
        </span>
        <h4 class="mb-2 fw-bold">Access pending</h4>
        <p class="text-muted mb-4">
          You are signed in as <span class="fw-semibold">{{ auth()->user()->name }}</span>, but no role has been
          assigned to your account yet. Please contact an administrator.
        </p>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button class="btn btn-primary" type="submit"><i class="ti ti-logout me-1"></i>Sign out</button>
        </form>
      </div>
    </div>
  </div>

  <script src="{{ asset('theme/js/vendors.min.js') }}"></script>
  <script src="{{ asset('theme/js/app.js') }}"></script>
</body>
</html>
