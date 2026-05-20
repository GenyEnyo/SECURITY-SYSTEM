<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
</head>
<body>

  <div class="login-shell">

    <!-- Brand poster column -->
    <section class="login-poster">
      <div class="ring r1"></div>
      <div class="ring r2"></div>

      <div class="brand-block">
        <div class="mark">M</div>
        <div>
          <div class="fw-7" style="font-size:14px;letter-spacing:.18em;opacity:.7;">M&nbsp;DASHBOARD</div>
          <div class="fw-7" style="font-size:18px;">Security System</div>
        </div>
      </div>

      <!-- <div>
        <h1>Track deployment, KPIs and incidents — in one place.</h1>
        <p class="blurb mt-3">Sign in to log today's scorecard, mark deployment attendance, and report incidents
           from the field. Your monthly reconciliation is built automatically.</p>
      </div> -->

      <div class="d-flex gap-4 align-items-center" style="opacity:.85; font-size:14px;">
        <div><i class="bi bi-camera"></i> The Camera never lies</div>
        <!-- <div><i class="bi bi-lock me-2"></i>Encrypted at rest</div>
        <div><i class="bi bi-graph-up me-2"></i>Audit log</div> -->
      </div>
    </section>

    <!-- Form column -->
    <section class="login-form-wrap">
      <form class="login-form" onsubmit="event.preventDefault(); window.location.href='/dashboard';">
        <h2>Welcome back</h2>
        <p class="lead">Sign in to continue to the dashboard.</p>

        <div class="mb-3">
          <label class="form-label" for="email">Email address</label>
          <div class="position-relative">
            <i class="bi bi-envelope position-absolute" style="left:18px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:18px;"></i>
            <input id="email" type="email" class="form-control ps-5" placeholder="kwasi@company.com" value="jane.doe@vra.com" required>
          </div>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-end">
            <label class="form-label" for="pw">Password</label>
            <!-- <a href="#" style="font-size:14px;">Forgot password?</a> -->
          </div>
          <div class="position-relative">
            <i class="bi bi-lock position-absolute" style="left:18px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:18px;"></i>
            <input id="pw" type="password" class="form-control ps-5 pe-5" placeholder="••••••••" value="password" required>
            <button type="button" id="togglePw" class="btn p-0" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);background:transparent;border:0;">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <!-- <div class="form-check mb-4">
          <input id="remember" class="form-check-input" type="checkbox" checked>
          <label class="form-check-label fw-7" for="remember">Keep me signed in for 30 days</label>
        </div> -->

        <button class="btn btn-primary btn-lg w-100" type="submit">
          <i class="bi bi-box-arrow-in-right me-2"></i>Sign in
        </button>

        <!-- <div class="text-center muted mt-4 fw-5" style="font-size:14px;">
          Need an account? Contact your administrator.
        </div> -->

        <!-- <div class="text-center mt-4" style="font-size:13px;">
          <a href="index.html" class="muted">← Back to overview</a>
        </div> -->
      </form>
    </section>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('togglePw').addEventListener('click', e => {
      const pw = document.getElementById('pw');
      const icon = e.currentTarget.querySelector('i');
      const show = pw.type === 'password';
      pw.type = show ? 'text' : 'password';
      icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  </script>
</body>
</html>
