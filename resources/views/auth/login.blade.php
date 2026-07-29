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
  <title>Login · M Dashboard</title>

  <link rel="shortcut icon" href="{{ asset('theme/images/favicon.ico') }}">

  <!-- Theme Config (must load early to avoid FOUC) -->
  <script src="{{ asset('theme/js/config.js') }}"></script>

  <!-- Vendor + App css -->
  <link href="{{ asset('theme/css/vendors.min.css') }}" rel="stylesheet" type="text/css">
  <link id="app-style" href="{{ asset('theme/css/app.min.css') }}" rel="stylesheet" type="text/css">
</head>

<body>

  <div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="card shadow-sm w-100" style="max-width:420px;">
      <div class="card-body p-4">

        <!-- Brand -->
        <div class="text-center mb-4">
          <span class="avatar-md bg-primary text-white rounded d-flex align-items-center justify-content-center mx-auto mb-3 fs-22 fw-bold">M</span>
          <h4 class="mb-1 fw-bold">M Dashboard</h4>
          <p class="text-muted mb-0">Security Operations</p>
        </div>

        <form onsubmit="event.preventDefault(); window.location.href='/dashboard';">
          <h5 class="mb-1">Welcome back</h5>
          <p class="text-muted">Sign in to continue to the dashboard.</p>

          <div class="mb-3">
            <label class="form-label" for="email">Email address</label>
            <input id="email" type="email" class="form-control" placeholder="kwasi@company.com" value="jane.doe@vra.com" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="pw">Password</label>
            <div class="position-relative">
              <input id="pw" type="password" class="form-control pe-5" placeholder="••••••••" value="password" required>
              <button type="button" id="togglePw" class="btn p-0 position-absolute text-muted border-0 bg-transparent"
                      style="right:14px;top:50%;transform:translateY(-50%);">
                <i class="ti ti-eye"></i>
              </button>
            </div>
          </div>

          <button class="btn btn-primary w-100" type="submit">
            <i class="ti ti-login me-1"></i>Sign in
          </button>
        </form>

      </div>
    </div>
  </div>

  <script src="{{ asset('theme/js/vendors.min.js') }}"></script>
  <script src="{{ asset('theme/js/app.js') }}"></script>
  <script>
    document.getElementById('togglePw').addEventListener('click', e => {
      const pw = document.getElementById('pw');
      const icon = e.currentTarget.querySelector('i');
      const show = pw.type === 'password';
      pw.type = show ? 'text' : 'password';
      icon.className = show ? 'ti ti-eye-off' : 'ti ti-eye';
    });
  </script>
</body>
</html>
