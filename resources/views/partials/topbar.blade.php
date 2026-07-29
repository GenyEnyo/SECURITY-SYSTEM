@php
  $authUser = auth()->user();
  $authName = $authUser->name ?? 'Officer';
  $authRole = $authUser->role ?? 'Security Operations';
  $initials = collect(explode(' ', trim($authName)))->filter()->take(2)->map(fn ($p) => strtoupper($p[0]))->implode('');
@endphp
<header class="app-topbar">
  <div class="container-fluid topbar-menu">
    <div class="d-flex align-items-center gap-2">

      <!-- Brand Logo (mobile/topbar) -->
      <div class="logo-topbar">
        <a href="{{ url('/dashboard') }}" class="logo-light">
          <span class="logo-lg fw-bold fs-18">M Dashboard</span>
          <span class="logo-sm fw-bold fs-18">M</span>
        </a>
        <a href="{{ url('/dashboard') }}" class="logo-dark">
          <span class="logo-lg fw-bold fs-18">M Dashboard</span>
          <span class="logo-sm fw-bold fs-18">M</span>
        </a>
      </div>

      <!-- Sidebar Menu Toggle Button -->
      <button class="sidenav-toggle-button btn btn-primary btn-icon">
        <i class="ti ti-menu-4"></i>
      </button>

      <!-- Search -->
      <div id="search-box-rounded" class="app-search d-none d-xl-flex">
        <input type="search" class="form-control rounded-pill topbar-search" name="search"
               placeholder="Quick search incidents, guards, locations…" />
        <i class="ti ti-search app-search-icon text-muted"></i>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">

      <!-- Notifications -->
      <div class="topbar-item">
        <button type="button" class="topbar-link btn btn-icon position-relative" aria-label="Notifications">
          <i class="ti ti-bell-ringing fs-22"></i>
          <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger p-1"><span class="visually-hidden">new alerts</span></span>
        </button>
      </div>

      <!-- User dropdown -->
      <div id="user-dropdown-detailed" class="topbar-item nav-user">
        <div class="dropdown">
          <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown" href="#!" aria-haspopup="false" aria-expanded="false">
            <span class="avatar avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-lg-2" style="width:32px;height:32px;">{{ $initials }}</span>
            <div class="d-lg-flex align-items-center gap-1 d-none">
              <span>
                <h5 class="my-0 lh-1 pro-username">{{ $authName }}</h5>
                <span class="fs-xs lh-1">{{ $authRole }}</span>
              </span>
              <i class="ti ti-chevron-down align-middle"></i>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-end">
            <div class="dropdown-header noti-title">
              <h6 class="text-overflow m-0">Welcome back 👋!</h6>
            </div>
            <a href="{{ url('/my-submissions') }}" class="dropdown-item">
              <i class="ti ti-folder me-1 fs-lg align-middle"></i>
              <span class="align-middle">My submissions</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item text-danger fw-semibold"
               onclick="event.preventDefault(); document.getElementById('topbar-logout-form').submit();">
              <i class="ti ti-logout me-1 fs-lg align-middle"></i>
              <span class="align-middle">Log Out</span>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <form id="topbar-logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
    @csrf
  </form>
</header>
