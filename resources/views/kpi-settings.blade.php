<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>KPI settings · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
  <link href="/assets/css/extras.css" rel="stylesheet">
</head>
<body data-page="kpi-settings" data-crumbs='[{"label":"Setups"},{"label":"KPI settings"}]'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>

    <div class="app-main">
      <header class="app-topbar" data-shell="topbar"></header>

      <main class="app-content">

        <div class="page-head">
          <div>
            <h1 class="page-title">KPI Settings</h1>
            <p class="page-subtitle">Configure KPI groups and sub-items used for daily scorecards</p>
          </div>
          <div class="actions">
            <button class="btn btn-outline-primary"><i class="bi bi-arrow-counterclockwise me-2"></i>Restore defaults</button>
            <button class="btn btn-success" onclick="window.mNotify('Settings saved','success');"><i class="bi bi-check-lg me-2"></i>Save changes</button>
          </div>
        </div>

        <!-- Weight banner -->
        <div class="weight-banner valid">
          <i class="bi bi-check-circle-fill"></i>
          <div><strong>Total weight: 100%</strong> — configuration is valid and ready for officer use.</div>
        </div>

        <!-- ============== Group 1 ============== -->
        <section class="kpi-group" data-group>
          <header>
            <div class="d-flex align-items-center gap-2">
              <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
              <div>
                <h4>Attendance &amp; Punctuality</h4>
                <div class="muted fw-5" style="font-size:12px;">Weight 30% · 3 sub-items</div>
              </div>
            </div>
            <div class="kpi-meta">
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" checked>
              </div>
              <button class="ra-btn" onclick="event.stopPropagation()"><i class="bi bi-pencil-square"></i></button>
              <button class="ra-btn danger" onclick="event.stopPropagation()"><i class="bi bi-trash"></i></button>
            </div>
          </header>
          <div class="body">
            <table class="kpi-table">
              <thead>
                <tr><th style="width:24px"></th><th>Sub-item</th><th>Target</th><th>Weight</th><th>Active</th><th class="actions-col">Actions</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td><span class="drag-handle"><i class="bi bi-grip-vertical"></i></span></td>
                  <td><input value="Guards reporting on time" style="width:100%;text-align:left;"></td>
                  <td><input value="42"></td>
                  <td><input value="10"></td>
                  <td><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" checked></div></td>
                  <td><div class="row-actions"><button class="ra-btn"><i class="bi bi-pencil-square"></i></button><button class="ra-btn danger"><i class="bi bi-trash"></i></button></div></td>
                </tr>
                <tr>
                  <td><span class="drag-handle"><i class="bi bi-grip-vertical"></i></span></td>
                  <td><input value="Shift handovers completed in 10 min" style="width:100%;text-align:left;"></td>
                  <td><input value="42"></td>
                  <td><input value="10"></td>
                  <td><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" checked></div></td>
                  <td><div class="row-actions"><button class="ra-btn"><i class="bi bi-pencil-square"></i></button><button class="ra-btn danger"><i class="bi bi-trash"></i></button></div></td>
                </tr>
                <tr>
                  <td><span class="drag-handle"><i class="bi bi-grip-vertical"></i></span></td>
                  <td><input value="Late arrivals (target &lt; 3)" style="width:100%;text-align:left;"></td>
                  <td><input value="3"></td>
                  <td><input value="10"></td>
                  <td><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" checked></div></td>
                  <td><div class="row-actions"><button class="ra-btn"><i class="bi bi-pencil-square"></i></button><button class="ra-btn danger"><i class="bi bi-trash"></i></button></div></td>
                </tr>
              </tbody>
            </table>
            <button class="btn btn-outline-primary btn-sm mt-3"><i class="bi bi-plus-square me-2"></i>Add sub-item</button>
          </div>
        </section>

        <!-- ============== Group 2 ============== -->
        <section class="kpi-group" data-group>
          <header>
            <div class="d-flex align-items-center gap-2">
              <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
              <div>
                <h4>Post Discipline &amp; Conduct</h4>
                <div class="muted fw-5" style="font-size:12px;">Weight 25% · 3 sub-items</div>
              </div>
            </div>
            <div class="kpi-meta">
              <div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" role="switch" checked></div>
              <button class="ra-btn"><i class="bi bi-pencil-square"></i></button>
              <button class="ra-btn danger"><i class="bi bi-trash"></i></button>
            </div>
          </header>
          <div class="body">
            <table class="kpi-table">
              <thead><tr><th style="width:24px"></th><th>Sub-item</th><th>Target</th><th>Weight</th><th>Active</th><th class="actions-col">Actions</th></tr></thead>
              <tbody>
                <tr><td><span class="drag-handle"><i class="bi bi-grip-vertical"></i></span></td><td><input value="Uniform & appearance" style="width:100%;text-align:left;"></td><td><input value="42"></td><td><input value="8"></td><td><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" checked></div></td><td><div class="row-actions"><button class="ra-btn"><i class="bi bi-pencil-square"></i></button><button class="ra-btn danger"><i class="bi bi-trash"></i></button></div></td></tr>
                <tr><td><span class="drag-handle"><i class="bi bi-grip-vertical"></i></span></td><td><input value="Logbook completeness" style="width:100%;text-align:left;"></td><td><input value="42"></td><td><input value="9"></td><td><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" checked></div></td><td><div class="row-actions"><button class="ra-btn"><i class="bi bi-pencil-square"></i></button><button class="ra-btn danger"><i class="bi bi-trash"></i></button></div></td></tr>
                <tr><td><span class="drag-handle"><i class="bi bi-grip-vertical"></i></span></td><td><input value="Complaints filed against officers" style="width:100%;text-align:left;"></td><td><input value="0"></td><td><input value="8"></td><td><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" checked></div></td><td><div class="row-actions"><button class="ra-btn"><i class="bi bi-pencil-square"></i></button><button class="ra-btn danger"><i class="bi bi-trash"></i></button></div></td></tr>
              </tbody>
            </table>
            <button class="btn btn-outline-primary btn-sm mt-3"><i class="bi bi-plus-square me-2"></i>Add sub-item</button>
          </div>
        </section>

        <!-- ============== Group 3 ============== -->
        <section class="kpi-group collapsed" data-group>
          <header>
            <div class="d-flex align-items-center gap-2">
              <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
              <div>
                <h4>Incident Response</h4>
                <div class="muted fw-5" style="font-size:12px;">Weight 25% · 2 sub-items</div>
              </div>
            </div>
            <div class="kpi-meta">
              <div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" role="switch" checked></div>
              <button class="ra-btn"><i class="bi bi-pencil-square"></i></button>
              <button class="ra-btn danger"><i class="bi bi-trash"></i></button>
            </div>
          </header>
          <div class="body"></div>
        </section>

        <!-- ============== Group 4 (inactive) ============== -->
        <section class="kpi-group collapsed" data-group style="opacity:.65;">
          <header>
            <div class="d-flex align-items-center gap-2">
              <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
              <div>
                <h4>Equipment &amp; Reporting</h4>
                <div class="muted fw-5" style="font-size:12px;">Weight 20% · 2 sub-items · INACTIVE</div>
              </div>
            </div>
            <div class="kpi-meta">
              <div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" role="switch"></div>
              <button class="ra-btn"><i class="bi bi-pencil-square"></i></button>
              <button class="ra-btn danger"><i class="bi bi-trash"></i></button>
            </div>
          </header>
          <div class="body"></div>
        </section>

        <button class="btn btn-primary mt-2"><i class="bi bi-plus-square me-2"></i>Add KPI group</button>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
  <script>
    document.querySelectorAll('.kpi-group > header').forEach(h => {
      h.addEventListener('click', e => {
        if (e.target.closest('.ra-btn,.form-check,.drag-handle,input')) return;
        h.parentElement.classList.toggle('collapsed');
      });
    });
  </script>
</body>
</html>
