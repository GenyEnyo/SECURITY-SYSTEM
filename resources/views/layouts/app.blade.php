<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'M Dashboard')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
  @stack('head')
</head>
<body data-page="@yield('page')" data-crumbs='@yield('crumbs', '[]')'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>

    <div class="app-main">
      @include('partials.navbar')

      <main class="app-content">
        @yield('content')
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
  <script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
  </script>
  @stack('scripts')
</body>
</html>
