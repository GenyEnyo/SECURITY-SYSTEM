<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
</head>
<body data-page="dashboard" data-crumbs='[{"label":"Dashboard"}]'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>

    <div class="app-main">
      <header class="app-topbar" data-shell="topbar"></header>

      <main class="app-content">

        <!-- Page head -->
        <div class="page-head">
          <div>
            <h1 class="page-title">Good morning, Kwasi</h1>
            <p class="page-subtitle">Monday, 18 May 2026 · Day shift</p>
          </div>
          <div class="actions">
            <a class="btn btn-success" href="/incidents/create"><i class="bi bi-shield-plus me-2"></i>Report incident</a>
            <a class="btn btn-primary" href="#"><i class="bi bi-clipboard-check me-2"></i>Record deployment</a>
          </div>
        </div>

        <!-- KPI alert banner -->
        <div class="alert d-flex align-items-center justify-content-between" role="alert"
             style="background:var(--brand-warning-soft);border:1px solid var(--brand-warning);border-radius:10px;padding:18px 22px;">
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-exclamation-octagon-fill" style="font-size:22px;color:var(--brand-warning);"></i>
            <div>
              <div class="fw-7" style="font-size:16px;">KPI scorecard not yet submitted</div>
              <div class="muted fw-5" style="font-size:14px;">Submit today's scorecard before 18:00 to avoid a late entry flag.</div>
            </div>
          </div>
          <a href="#" class="btn btn-warning"><i class="bi bi-pencil-square me-2"></i>Submit now</a>
        </div>

        <!-- Metric cards -->
        <div class="row g-3 mt-3">
          <div class="col-md-6 col-xl-3">
            <div class="metric-card">
              <div class="icon-wrap"><i class="bi bi-people-fill"></i></div>
              <div class="label">Contracted today</div>
              <div class="value">42</div>
              <div class="delta up"><i class="bi bi-arrow-up"></i> 2 vs. last shift</div>
            </div>
          </div>
          <div class="col-md-6 col-xl-3">
            <div class="metric-card">
              <div class="icon-wrap" style="background:rgba(61,179,110,.12);color:var(--brand-success);"><i class="bi bi-check2-circle"></i></div>
              <div class="label">Actual deployed</div>
              <div class="value">38</div>
              <div class="delta down"><i class="bi bi-arrow-down"></i> 4 short</div>
            </div>
          </div>
          <div class="col-md-6 col-xl-3">
            <div class="metric-card">
              <div class="icon-wrap" style="background:rgba(255,169,31,.18);color:var(--brand-warning);"><i class="bi bi-graph-up-arrow"></i></div>
              <div class="label">Deployment %</div>
              <div class="value">90.5%</div>
              <div class="delta down"><i class="bi bi-arrow-down"></i> Target 100%</div>
            </div>
          </div>
          <div class="col-md-6 col-xl-3">
            <div class="metric-card">
              <div class="icon-wrap" style="background:rgba(252,51,32,.12);color:var(--brand-danger);"><i class="bi bi-shield-exclamation"></i></div>
              <div class="label">Incidents this week</div>
              <div class="value">5</div>
              <div class="delta up" style="color:var(--brand-danger);"><i class="bi bi-arrow-up"></i> 2 vs. last week</div>
            </div>
          </div>
        </div>

        <!-- Two-column: recent incidents + week chart -->
        <div class="row g-3 mt-3">
          <!-- Recent incidents -->
          <div class="col-lg-7">
            <div class="shadow-card p-4">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-7" style="font-size:20px;">Recent incidents</div>
                <a href="/incidents" class="fw-7" style="font-size:14px;">View all <i class="bi bi-arrow-right"></i></a>
              </div>
              <table class="table mb-0" style="font-size:15px;">
                <thead style="color:var(--text-muted); font-size:13px; text-transform:uppercase; letter-spacing:.06em;">
                  <tr><th>Date</th><th>Type</th><th>Location</th><th>Severity</th><th>Status</th></tr>
                </thead>
                <tbody class="fw-5">
                  <tr>
                    <td>05/11/26</td><td>Item Loss</td><td>Akuse</td>
                    <td><span class="pill pill-low">Low</span></td>
                    <td><span class="pill pill-reviewing">Reviewing</span></td>
                  </tr>
                  <tr>
                    <td>23/10/26</td><td>Injury</td><td>Accra</td>
                    <td><span class="pill pill-low">Low</span></td>
                    <td><span class="pill pill-resolved">Resolved</span></td>
                  </tr>
                  <tr>
                    <td>10/10/26</td><td>Damage</td><td>Aboadze</td>
                    <td><span class="pill pill-medium">Medium</span></td>
                    <td><span class="pill pill-reported">Reported</span></td>
                  </tr>
                  <tr>
                    <td>14/09/26</td><td>Theft</td><td>Akosombo</td>
                    <td><span class="pill pill-urgent">Urgent</span></td>
                    <td><span class="pill pill-escalated">Escalated</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Week chart -->
          <div class="col-lg-5">
            <div class="shadow-card p-4 h-100">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-7" style="font-size:20px;">Incidents this week</div>
                <span class="muted fw-7" style="font-size:13px;">Mon — Sun</span>
              </div>
              <div style="display:flex;align-items:flex-end;gap:14px;height:200px;padding:10px 0;">
                <!-- Each bar -->
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <div style="width:100%;height:30%;background:var(--brand-primary-tint);border-radius:6px 6px 0 0;"></div>
                  <div class="muted fw-7" style="font-size:12px;">Mon</div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <div style="width:100%;height:50%;background:var(--brand-primary);border-radius:6px 6px 0 0;"></div>
                  <div class="muted fw-7" style="font-size:12px;">Tue</div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <div style="width:100%;height:15%;background:var(--brand-primary-tint);border-radius:6px 6px 0 0;"></div>
                  <div class="muted fw-7" style="font-size:12px;">Wed</div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <div style="width:100%;height:80%;background:var(--brand-warning);border-radius:6px 6px 0 0;"></div>
                  <div class="muted fw-7" style="font-size:12px;">Thu</div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <div style="width:100%;height:40%;background:var(--brand-primary);border-radius:6px 6px 0 0;"></div>
                  <div class="muted fw-7" style="font-size:12px;">Fri</div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <div style="width:100%;height:25%;background:var(--brand-primary-tint);border-radius:6px 6px 0 0;"></div>
                  <div class="muted fw-7" style="font-size:12px;">Sat</div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <div style="width:100%;height:60%;background:var(--brand-danger);border-radius:6px 6px 0 0;"></div>
                  <div class="muted fw-7" style="font-size:12px;">Sun</div>
                </div>
              </div>
              <div class="d-flex gap-3 mt-3" style="font-size:12px;">
                <div class="d-flex align-items-center gap-2"><span style="width:10px;height:10px;background:var(--brand-primary);border-radius:2px;"></span> Normal</div>
                <div class="d-flex align-items-center gap-2"><span style="width:10px;height:10px;background:var(--brand-warning);border-radius:2px;"></span> Elevated</div>
                <div class="d-flex align-items-center gap-2"><span style="width:10px;height:10px;background:var(--brand-danger);border-radius:2px;"></span> Critical</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick actions row -->
        <div class="section-divider">Quick actions</div>
        <div class="row g-3">
          <div class="col-sm-6 col-lg-3">
            <a href="/incidents/create" class="metric-card d-flex align-items-center gap-3" style="text-decoration:none;color:inherit;">
              <div class="icon-wrap mb-0" style="background:rgba(252,51,32,.12);color:var(--brand-danger);"><i class="bi bi-shield-plus"></i></div>
              <div>
                <div class="fw-7">Report incident</div>
                <div class="muted fw-5" style="font-size:13px;">New entry form</div>
              </div>
            </a>
          </div>
          <div class="col-sm-6 col-lg-3">
            <a href="#" class="metric-card d-flex align-items-center gap-3" style="text-decoration:none;color:inherit;">
              <div class="icon-wrap mb-0"><i class="bi bi-clipboard-check"></i></div>
              <div>
                <div class="fw-7">Deployment log</div>
                <div class="muted fw-5" style="font-size:13px;">Today's attendance</div>
              </div>
            </a>
          </div>
          <div class="col-sm-6 col-lg-3">
            <a href="#" class="metric-card d-flex align-items-center gap-3" style="text-decoration:none;color:inherit;">
              <div class="icon-wrap mb-0" style="background:rgba(255,169,31,.18);color:var(--brand-warning);"><i class="bi bi-stars"></i></div>
              <div>
                <div class="fw-7">Submit KPI scorecard</div>
                <div class="muted fw-5" style="font-size:13px;">Daily form</div>
              </div>
            </a>
          </div>
          <div class="col-sm-6 col-lg-3">
            <a href="#" class="metric-card d-flex align-items-center gap-3" style="text-decoration:none;color:inherit;">
              <div class="icon-wrap mb-0" style="background:rgba(61,179,110,.12);color:var(--brand-success);"><i class="bi bi-clock-history"></i></div>
              <div>
                <div class="fw-7">My submissions</div>
                <div class="muted fw-5" style="font-size:13px;">History &amp; exports</div>
              </div>
            </a>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
</body>
</html>
