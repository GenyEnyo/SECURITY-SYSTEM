@php
  // Mark a side-nav link active when the current request path matches any of $patterns.
  $navActive = fn (...$patterns) => collect($patterns)->contains(fn ($p) => request()->is($p)) ? 'active' : '';
@endphp
<div class="sidenav-menu">

  <!-- Brand Logo -->
  <a href="{{ url('/dashboard') }}" class="logo">
    <span class="logo logo-light">
      <span class="logo-lg fw-bold fs-20 text-white">M Dashboard</span>
      <span class="logo-sm fw-bold fs-20 text-white">M</span>
    </span>
    <span class="logo logo-dark">
      <span class="logo-lg fw-bold fs-20">M Dashboard</span>
      <span class="logo-sm fw-bold fs-20">M</span>
    </span>
  </a>

  <!-- Sidebar Hover Menu Toggle Button -->
  <button class="button-on-hover">
    <span class="btn-on-hover-icon"></span>
  </button>

  <!-- Full Sidebar Menu Close Button -->
  <button class="button-close-offcanvas">
    <i class="ti ti-menu-4 align-middle"></i>
  </button>

  <div class="scrollbar" data-simplebar="">
    <div id="sidenav-menu">
      <ul class="side-nav">

        <li class="side-nav-title mt-2">Dashboards</li>
        <li class="side-nav-item">
          <a href="{{ url('/dashboard') }}" class="side-nav-link {{ $navActive('dashboard') }}">
            <span class="menu-icon"><i class="ti ti-gauge"></i></span>
            <span class="menu-text">Officer dashboard</span>
          </a>
        </li>

        <li class="side-nav-title">Daily entry</li>
        <li class="side-nav-item">
          <a href="{{ url('/kpi/entries') }}" class="side-nav-link {{ $navActive('kpi/entries*') }}">
            <span class="menu-icon"><i class="ti ti-clipboard-check"></i></span>
            <span class="menu-text">KPI scorecard</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/incidents/create') }}" class="side-nav-link {{ $navActive('incidents/create') }}">
            <span class="menu-icon"><i class="ti ti-square-plus"></i></span>
            <span class="menu-text">Report incident</span>
          </a>
        </li>

        <li class="side-nav-title">Records</li>
        <li class="side-nav-item">
          <a href="{{ url('/incidents') }}" class="side-nav-link {{ $navActive('incidents') }}">
            <span class="menu-icon"><i class="ti ti-alert-triangle"></i></span>
            <span class="menu-text">Incidents</span>
            <span class="badge bg-danger rounded-pill ms-auto">5</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/incidents/all') }}" class="side-nav-link {{ $navActive('incidents/all') }}">
            <span class="menu-icon"><i class="ti ti-shield"></i></span>
            <span class="menu-text">All incidents</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/kpi/records') }}" class="side-nav-link {{ $navActive('kpi/records*') }}">
            <span class="menu-icon"><i class="ti ti-list-check"></i></span>
            <span class="menu-text">KPI Records</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/my-submissions') }}" class="side-nav-link {{ $navActive('my-submissions') }}">
            <span class="menu-icon"><i class="ti ti-folder"></i></span>
            <span class="menu-text">My submissions</span>
          </a>
        </li>

        <li class="side-nav-title">Reports</li>
        <li class="side-nav-item">
          <a href="{{ url('/kpi/reports') }}" class="side-nav-link {{ request()->is('kpi/reports') ? 'active' : '' }}">
            <span class="menu-icon"><i class="ti ti-chart-line"></i></span>
            <span class="menu-text">KPI Reports</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/kpi/reports/monthly') }}" class="side-nav-link {{ $navActive('kpi/reports/monthly') }}">
            <span class="menu-icon"><i class="ti ti-file-text"></i></span>
            <span class="menu-text">Monthly report</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/kpi/compliance') }}" class="side-nav-link {{ $navActive('kpi/compliance*') }}">
            <span class="menu-icon"><i class="ti ti-calendar-stats"></i></span>
            <span class="menu-text">Deployment compliance</span>
          </a>
        </li>

        <li class="side-nav-title">Setups</li>
        <li class="side-nav-item">
          <a href="{{ url('/kpi/settings') }}" class="side-nav-link {{ $navActive('kpi/settings*') }}">
            <span class="menu-icon"><i class="ti ti-adjustments"></i></span>
            <span class="menu-text">KPI settings</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/locations') }}" class="side-nav-link {{ $navActive('locations*') }}">
            <span class="menu-icon"><i class="ti ti-map-pin"></i></span>
            <span class="menu-text">Locations</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/deployments') }}" class="side-nav-link {{ $navActive('deployments*', 'buildings/*/deployments*') }}">
            <span class="menu-icon"><i class="ti ti-clipboard-list"></i></span>
            <span class="menu-text">Deployments</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/security-companies') }}" class="side-nav-link {{ $navActive('security-companies*') }}">
            <span class="menu-icon"><i class="ti ti-shield-lock"></i></span>
            <span class="menu-text">Security companies</span>
          </a>
        </li>
        <li class="side-nav-item">
          <a href="{{ url('/users') }}" class="side-nav-link {{ $navActive('users*') }}">
            <span class="menu-icon"><i class="ti ti-users"></i></span>
            <span class="menu-text">Users</span>
          </a>
        </li>

        <li class="side-nav-title">Settings</li>
        @php $settingsOpen = request()->is('settings/*'); @endphp
        <li class="side-nav-item">
          <a data-bs-toggle="collapse" href="#sidebarSettings" aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}" aria-controls="sidebarSettings" class="side-nav-link">
            <span class="menu-icon"><i class="ti ti-settings"></i></span>
            <span class="menu-text">Settings</span>
            <span class="menu-arrow"></span>
          </a>
          <div class="collapse {{ $settingsOpen ? 'show' : '' }}" id="sidebarSettings">
            <ul class="sub-menu">
              <li class="side-nav-item">
                <a href="{{ url('/settings/incident-types') }}" class="side-nav-link {{ $navActive('settings/incident-types*') }}">
                  <span class="menu-text">Incident Types</span>
                </a>
              </li>
              <li class="side-nav-item">
                <a href="{{ url('/settings/severity-levels') }}" class="side-nav-link {{ $navActive('settings/severity-levels*') }}">
                  <span class="menu-text">Severity Level</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <li class="side-nav-title">Resources</li>
        <li class="side-nav-item">
          <a href="#" class="side-nav-link"
             onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
            <span class="menu-icon"><i class="ti ti-logout"></i></span>
            <span class="menu-text">Sign out</span>
          </a>
        </li>

      </ul>
    </div>
  </div>

  <form id="sidebar-logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
    @csrf
  </form>
</div>
