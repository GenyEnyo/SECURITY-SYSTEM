/* ---------------------------------------------------------------------
   M Dashboard — shared partials (sidebar + topbar)
   --------------------------------------------------------------------- */
(function () {
  const navGroups = [
    { title: 'Dashboards', items: [
      { id: 'dashboard',       label: 'Officer dashboard',    icon: 'bi-speedometer2',        href: '/dashboard' },
    ]},
    { title: 'Daily entry', items: [
      { id: 'kpi-entry',       label: 'KPI scorecard',        icon: 'bi-clipboard-check',     href: '/kpi/entries' },
      { id: 'add-incident',    label: 'Report incident',      icon: 'bi-plus-square',         href: '/incidents/create' },
    ]},
    { title: 'Records', items: [
      { id: 'incidents',       label: 'Incidents',            icon: 'bi-shield-exclamation',  href: '/incidents', badge: '5' },
      { id: 'all-incidents',   label: 'All incidents',        icon: 'bi-shield-shaded',       href: '/incidents/all' },
      { id: 'my-submissions',  label: 'My submissions',       icon: 'bi-folder2-open',        href: '/my-submissions' },
    ]},
    { title: 'Setups', items: [
      { id: 'kpi-settings',    label: 'KPI settings',         icon: 'bi-sliders',             href: '/kpi/settings' },
      { id: 'locations',       label: 'Locations',            icon: 'bi-geo-alt',             href: '/locations' },
      { id: 'security-companies', label: 'Security companies', icon: 'bi-shield-lock',        href: '/security-companies' },
      { id: 'users',           label: 'Users',                icon: 'bi-people',              href: '/users' },
    ]},
    { title: 'Settings', items: [
      { id: 'settings', label: 'Settings', icon: 'bi-gear', children: [
        { id: 'incident-types',  label: 'Incident Types', icon: 'bi-tag',       href: '/settings/incident-types' },
        { id: 'severity-levels', label: 'Severity Level', icon: 'bi-bar-chart', href: '/settings/severity-levels' },
      ]},
    ]},
    { title: 'Resources', items: [
      { id: 'logout',          label: 'Sign out',             icon: 'bi-box-arrow-right',     href: '#', logout: true },
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
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    navGroups.forEach(g => {
      if (g.title) html += `<div class="nav-section">${g.title}</div>`;
      g.items.forEach(it => {
        const isActive = it.id === active ? ' active' : '';
        const badge = it.badge ? `<span class="sidebar-badge">${it.badge}</span>` : '';
        if (it.children) {
          const childActive = it.children.some(c => c.id === active);
          const openCls = childActive ? ' open' : '';
          const hiddenCls = childActive ? '' : ' d-none';
          html += `<button type="button" class="nav-link sidebar-link sidebar-parent${openCls}"
                            onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('d-none')"
                            style="width:100%;background:none;border:0;text-align:left;display:flex;align-items:center;">
                      <i class="bi ${it.icon}"></i><span>${it.label}</span>
                      <i class="bi bi-chevron-down ms-auto" style="font-size:11px;"></i>
                    </button>
                    <div class="sidebar-children${hiddenCls}" style="padding-left:24px;">`;
          it.children.forEach(c => {
            const cActive = c.id === active ? ' active' : '';
            html += `<a class="nav-link sidebar-link${cActive}" href="${c.href}">
                        <i class="bi ${c.icon}"></i><span>${c.label}</span>
                     </a>`;
          });
          html += `</div>`;
        } else if (it.logout) {
          html += `<form action="/logout" method="POST" style="margin:0;">
                      <input type="hidden" name="_token" value="${csrf}">
                      <button type="submit" class="nav-link sidebar-link${isActive}" style="width:100%;background:none;border:0;text-align:left;">
                        <i class="bi ${it.icon}"></i><span>${it.label}</span>
                      </button>
                   </form>`;
        } else {
          html += `<a class="nav-link sidebar-link${isActive}" href="${it.href}">
                      <i class="bi ${it.icon}"></i><span>${it.label}</span>${badge}
                   </a>`;
        }
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
    let crumbs = [{ label: 'Home', href: '/dashboard' }];
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
