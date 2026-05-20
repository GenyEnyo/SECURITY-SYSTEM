<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>KPI scorecard · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
  <link href="/assets/css/extras.css" rel="stylesheet">
</head>
<body data-page="kpi-entry" data-crumbs='[{"label":"Daily entry"},{"label":"KPI scorecard"}]'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>

    <div class="app-main">
      <header class="app-topbar" data-shell="topbar"></header>

      <main class="app-content" style="padding-bottom:120px;">

        <!-- Page head -->
        <div class="page-head">
          <div>
            <h1 class="page-title">KPI Scorecard — Daily entry</h1>
            <p class="page-subtitle">Record today's performance scores for each KPI group</p>
          </div>
          <div class="actions">
            <input type="date" class="form-control" id="kpi-date" value="2026-05-18" style="width:160px;">
            <!-- <button class="btn btn-outline-primary"><i class="bi bi-save me-2"></i>Save draft</button>
            <button class="btn btn-success" onclick="window.mNotify('Scorecard submitted','success'); setTimeout(()=>location.href='/dashboard',900);">
              <i class="bi bi-check-lg me-2"></i>Submit scorecard
            </button> -->
          </div>
        </div>

        <!-- Weight banner — auto-calculated -->
        <!-- <div class="weight-banner valid">
          <i class="bi bi-check-circle-fill"></i>
          <div>Active groups total <strong>100%</strong> — scoring is enabled.</div>
        </div> -->

        <!-- =================== KPI Group 1 =================== -->
        <section class="kpi-group" data-group>
          <header onclick="this.parentElement.classList.toggle('collapsed')">
            <div>
              <h4>Bearing &amp; App 10</h4>
              <div class="muted fw-5" style="font-size:12px;">weight 10%</div>
            </div>
            <div class="kpi-meta">
              <span>Group score <strong id="g1-score" style="color:var(--text-strong);font-size:14px;">88%</strong></span>
              <i class="bi bi-chevron-down chev" style="font-size:16px;"></i>
            </div>
          </header>
          <div class="body">
            <table class="kpi-table">
              <thead>
                <tr>
                  <th style="width:48%">Sub-item</th>
                  <th>Target</th>
                  <th>Scored</th>
                  <th>Per %</th>
                  <th>Merit</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Criteria</td>
                  <td><input type="number" value="42"></td>
                  <td><input type="number" value="40"></td>
                  <td class="per-cell per-green">95.2%</td>
                  <td><input type="number" value="9.5"></td>
                </tr>
                <tr>
                  <td>Turnout</td>
                  <td><input type="number" value="42"></td>
                  <td><input type="number" value="32"></td>
                  <td class="per-cell per-amber">76.2%</td>
                  <td><input type="number" value="7.6"></td>
                </tr>
                <tr>
                  <td>Neatness</td>
                  <td><input type="number" value="3"></td>
                  <td><input type="number" value="6"></td>
                  <td class="per-cell per-red">50.0%</td>
                  <td><input type="number" value="5.0"></td>
                </tr>
                <tr>
                  <td>Equipped</td>
                  <td><input type="number" value="3"></td>
                  <td><input type="number" value="6"></td>
                  <td class="per-cell per-red">50.0%</td>
                  <td><input type="number" value="5.0"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- =================== KPI Group 2 =================== -->
        <section class="kpi-group" data-group>
          <header onclick="this.parentElement.classList.toggle('collapsed')">
            <div>
              <h4>Post Discipline &amp; Conduct</h4>
              <div class="muted fw-5" style="font-size:12px;">3 sub-items · weight 25%</div>
            </div>
            <div class="kpi-meta">
              <span>Group score <strong style="color:var(--text-strong);font-size:14px;">92%</strong></span>
              <i class="bi bi-chevron-down chev" style="font-size:16px;"></i>
            </div>
          </header>
          <div class="body">
            <table class="kpi-table">
              <thead>
                <tr><th style="width:48%">Sub-item</th><th>Target</th><th>Scored</th><th>Per %</th><th>Merit</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td>Uniform &amp; appearance</td>
                  <td><input value="42"></td><td><input value="42"></td>
                  <td class="per-cell per-green">100%</td>
                  <td><input value="10"></td>
                </tr>
                <tr>
                  <td>Logbook completeness</td>
                  <td><input value="42"></td><td><input value="38"></td>
                  <td class="per-cell per-green">90.5%</td>
                  <td><input value="9"></td>
                </tr>
                <tr>
                  <td>Complaints filed against officers</td>
                  <td><input value="0"></td><td><input value="1"></td>
                  <td class="per-cell per-amber">75.0%</td>
                  <td><input value="7.5"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- =================== KPI Group 3 =================== -->
        <section class="kpi-group" data-group>
          <header onclick="this.parentElement.classList.toggle('collapsed')">
            <div>
              <h4>Incident Response</h4>
              <div class="muted fw-5" style="font-size:12px;">2 sub-items · weight 25%</div>
            </div>
            <div class="kpi-meta">
              <span>Group score <strong style="color:var(--text-strong);font-size:14px;">82%</strong></span>
              <i class="bi bi-chevron-down chev" style="font-size:16px;"></i>
            </div>
          </header>
          <div class="body">
            <table class="kpi-table">
              <thead>
                <tr><th style="width:48%">Sub-item</th><th>Target</th><th>Scored</th><th>Per %</th><th>Merit</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td>Avg. response time (min)</td>
                  <td><input value="2"></td><td><input value="3"></td>
                  <td class="per-cell per-amber">66.7%</td>
                  <td><input value="6.7"></td>
                </tr>
                <tr>
                  <td>Incidents documented within 30 min</td>
                  <td><input value="100"></td><td><input value="98"></td>
                  <td class="per-cell per-green">98.0%</td>
                  <td><input value="9.8"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- =================== KPI Group 4 =================== -->
        <section class="kpi-group collapsed" data-group>
          <header onclick="this.parentElement.classList.toggle('collapsed')">
            <div>
              <h4>Equipment &amp; Reporting</h4>
              <div class="muted fw-5" style="font-size:12px;">2 sub-items · weight 20%</div>
            </div>
            <div class="kpi-meta">
              <span>Group score <strong style="color:var(--text-strong);font-size:14px;">95%</strong></span>
              <i class="bi bi-chevron-down chev" style="font-size:16px;"></i>
            </div>
          </header>
          <div class="body">
            <table class="kpi-table">
              <thead>
                <tr><th style="width:48%">Sub-item</th><th>Target</th><th>Scored</th><th>Per %</th><th>Merit</th></tr>
              </thead>
              <tbody>
                <tr><td>Radio check-ins on schedule</td><td><input value="24"></td><td><input value="23"></td><td class="per-cell per-green">95.8%</td><td><input value="9.6"></td></tr>
                <tr><td>Daily report submitted on time</td><td><input value="1"></td><td><input value="1"></td><td class="per-cell per-green">100%</td><td><input value="10"></td></tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Comments -->
        <div class="form-card mt-4">
          <label class="field-label">Officer comments <span class="muted fw-5">(optional)</span></label>
          <textarea class="brand-input" rows="3" placeholder="Note anything unusual about today's shift..."></textarea>
        </div>

        <!-- Sticky total bar -->
        <div class="kpi-total-bar">
          <div class="stat"><span class="lbl">Date</span><span class="val" style="font-size:14px;">Mon, 18 May 2026</span></div>
          <div class="stat"><span class="lbl">Groups</span><span class="val">4</span></div>
          <div class="stat"><span class="lbl">Sub-items</span><span class="val">10</span></div>
          <div class="stat"><span class="lbl">Total weight</span><span class="val">100%</span></div>
          <div class="stat"><span class="lbl">Total merit</span><span class="val" style="color:var(--brand-success);">88.7 / 100</span></div>
          <div class="ms-auto d-flex gap-2">
            <button class="btn btn-outline-primary"><i class="bi bi-eye me-2"></i>Preview</button>
            <button class="btn btn-success" onclick="window.mNotify('Scorecard submitted','success'); setTimeout(()=>location.href='/dashboard',900);">
              <i class="bi bi-check-lg me-2"></i>Submit
            </button>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
  <script>
    // Auto-calculate Per % and color it on input
    function recalc(row){
      const tgt = +row.cells[1].querySelector('input').value || 0;
      const got = +row.cells[2].querySelector('input').value || 0;
      const pct = tgt ? Math.min((got/tgt)*100, 200) : 0;
      const cell = row.cells[3];
      cell.textContent = pct.toFixed(1) + '%';
      cell.classList.remove('per-green','per-amber','per-red');
      if (pct >= 80) cell.classList.add('per-green');
      else if (pct >= 50) cell.classList.add('per-amber');
      else cell.classList.add('per-red');
      row.cells[4].querySelector('input').value = (pct/10).toFixed(1);
    }
    document.querySelectorAll('.kpi-table tbody tr').forEach(tr => {
      tr.querySelectorAll('input[type=number],input').forEach(i => i.addEventListener('input', () => recalc(tr)));
    });
  </script>
</body>
</html>
