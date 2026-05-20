/* ---------------------------------------------------------------------
   M Dashboard — shared partials (sidebar + topbar)
   --------------------------------------------------------------------- */
(function () {
  const navGroups = [
    { title: 'Dashboards', items: [
      { id: 'dashboard',       label: 'Officer dashboard',    icon: 'bi-speedometer2',    href: 'dashboard.html' },
      { id: 'head-dashboard',  label: 'Head of Security',     icon: 'bi-bar-chart-line',  href: 'head-dashboard.html' },
    ]},
    { title: 'Daily entry', items: [
      { id: 'kpi-entry',       label: 'KPI scorecard',        icon: 'bi-clipboard-check',     href: 'kpi-entry.html' },
      { id: 'deployment-log',  label: 'Deployment log',       icon: 'bi-people',              href: 'deployment-log.html' },
      { id: 'add-incident',    label: 'Report incident',      icon: 'bi-plus-square',         href: 'add-incident.html' },
    ]},
    { title: 'Records', items: [
      { id: 'incidents',       label: 'Incidents',            icon: 'bi-shield-exclamation',  href: 'incidents.html', badge: '5' },
      { id: 'all-incidents',   label: 'All incidents (Head)', icon: 'bi-shield-shaded',       href: 'all-incidents.html' },
      { id: 'history',         label: 'Incident history',     icon: 'bi-clock-history',       href: 'incident-history.html' },
      { id: 'my-submissions',  label: 'My submissions',       icon: 'bi-folder2-open',        href: 'my-submissions.html' },
    ]},
    { title: 'Reports', items: [
      { id: 'kpi-reports',     label: 'KPI reports',          icon: 'bi-graph-up',            href: 'kpi-reports.html' },
      { id: 'reconciliation',  label: 'Monthly reconciliation', icon: 'bi-calculator',        href: 'reconciliation.html' },
      { id: 'invoices',        label: 'Vendor invoices',      icon: 'bi-receipt',             href: 'invoices.html' },
    ]},
    { title: 'Setups', items: [
      { id: 'kpi-settings',    label: 'KPI settings',         icon: 'bi-sliders',             href: 'kpi-settings.html' },
      { id: 'guards',          label: 'Guards',               icon: 'bi-person-badge',        href: 'guards.html' },
      { id: 'guards-directory',label: 'Guards directory',     icon: 'bi-book',                href: 'guards-directory.html' },
      { id: 'vendors',         label: 'Vendors',              icon: 'bi-building',            href: 'vendors.html' },
      { id: 'locations',       label: 'Locations',            icon: 'bi-geo-alt',             href: 'locations.html' },
      { id: 'users',           label: 'Users',                icon: 'bi-people',              href: 'users.html' },
      { id: 'system-settings', label: 'System settings',      icon: 'bi-gear',                href: 'system-settings.html' },
    ]},
    { title: 'Resources', items: [
      { id: 'design',          label: 'Design system',        icon: 'bi-palette',             href: 'design-system.html' },
      { id: 'logout',          label: 'Sign out',             icon: 'bi-box-arrow-right',     href: 'login.html' },
    ]},
  ];

  function renderSidebar(active) {
    let html = `
      <div class="brand">
        <div class="brand-mark">M</div>
        <div>
          <div class="brand-name">M Dashboard</div>
          <div class="brand-sub">Security Operations</div>
        </div>
      </div>
      <nav>`;
    navGroups.forEach(g => {
      if (g.title) html += `<div class="nav-section">${g.title}</div>`;
      g.items.forEach(it => {
        const isActive = it.id === active ? ' active' : '';
        const badge = it.badge ? `<span class="sidebar-badge">${it.badge}</span>` : '';
        html += `<a class="nav-link sidebar-link${isActive}" href="${it.href}">
                    <i class="bi ${it.icon}"></i><span>${it.label}</span>${badge}
                 </a>`;
      });
    });
    html += `</nav>`;
    return html;
  }

  function renderTopbar(crumbs) {
    const crumbHtml = (crumbs || []).map((c, i, arr) => {
      const last = i === arr.length - 1;
      return last
        ? `<span class="fw-7">${c.label}</span>`
        : `<a href="${c.href || '#'}" class="muted">${c.label}</a><i class="bi bi-chevron-right muted" style="font-size:11px"></i>`;
    }).join('');

    return `
      <nav aria-label="breadcrumb" style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:500;">
        ${crumbHtml}
      </nav>
      <div class="topbar-search">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Quick search incidents, guards, locations…">
      </div>
      <div class="topbar-actions">
        <button class="icon-btn" aria-label="Notifications">
          <i class="bi bi-bell"></i><span class="dot"></span>
        </button>
        <button class="icon-btn" aria-label="Help"><i class="bi bi-question-circle"></i></button>
        <div class="d-flex align-items-center gap-2">
          <div class="avatar">KA</div>
          <div class="d-none d-md-block">
            <div class="fw-7" style="font-size:13px;line-height:1.1">Kwasi Ansah</div>
            <div class="muted" style="font-size:11px">Senior Officer</div>
          </div>
        </div>
      </div>
    `;
  }

  document.addEventListener('DOMContentLoaded', () => {
    const active = document.body.dataset.page;
    const crumbsAttr = document.body.dataset.crumbs;
    let crumbs = [{ label: 'Home', href: 'dashboard.html' }];
    if (crumbsAttr) {
      try { crumbs = crumbs.concat(JSON.parse(crumbsAttr)); }
      catch (e) {}
    }

    const sb = document.querySelector('[data-shell="sidebar"]');
    if (sb) sb.innerHTML = renderSidebar(active);

    const tb = document.querySelector('[data-shell="topbar"]');
    if (tb) tb.innerHTML = renderTopbar(crumbs);
  });
})();
