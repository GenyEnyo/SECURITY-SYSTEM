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
            <input type="date" class="form-control" id="kpi-date" value="{{ now()->toDateString() }}" style="width:160px;">
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

        @if (session('status'))
          <div class="alert alert-success" role="alert" style="background:rgba(61,179,110,.12);border:1px solid var(--brand-success);border-radius:10px;padding:12px 18px;">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger" role="alert" style="background:rgba(252,51,32,.10);border:1px solid var(--brand-danger);border-radius:10px;padding:12px 18px;">
            @foreach ($errors->all() as $message)
              <div><i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}</div>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('entries.store') }}">
          @csrf
          <input type="hidden" name="location_id" id="kpi-loc-input">
          <input type="hidden" name="building_id" id="kpi-building-input">
          <input type="hidden" name="date" id="kpi-date-input">

        <!-- Deployment location selector -->
        <div class="form-card mb-4">
          <div class="row g-3">
            <div class="col-lg-4">
              <label class="field-label" for="kpi-loc">Location</label>
              <select id="kpi-loc" class="brand-input brand-select">
                <option value="" disabled selected>Select a location...</option>
                @foreach ($locations as $location)
                  <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-lg-4">
              <label class="field-label" for="kpi-building">Building</label>
              <select id="kpi-building" class="brand-input brand-select" disabled>
                <option value="" disabled selected>Select a location first...</option>
              </select>
            </div>
            <div class="col-lg-4">
              <label class="field-label" for="kpi-place">Specific place <span class="muted fw-5">(optional)</span></label>
              <select id="kpi-place" class="brand-input brand-select" disabled>
                <option value="">All places in building</option>
              </select>
            </div>
          </div>
        </div>

        @forelse ($groups as $group)
          @php $isDeployment = strtolower(trim($group->name)) === 'deployment'; @endphp
          <section class="kpi-group" data-group data-weight="{{ $group->weight }}">
            <header onclick="this.parentElement.classList.toggle('collapsed')">
              <div>
                <h4>{{ $group->name }}</h4>
                <div class="muted fw-5" style="font-size:12px;">
                  {{ $group->subItems->count() }} sub-item{{ $group->subItems->count() === 1 ? '' : 's' }} · weight {{ $group->weight }}%
                </div>
              </div>
              <div class="kpi-meta">
                <i class="bi bi-chevron-down chev" style="font-size:16px;"></i>
              </div>
            </header>
            <div class="body">
              @if ($isDeployment)
                <div id="kpi-deploy-summary" class="muted fw-5 mb-3" style="font-size:13px;">
                  Select a building and date to load the deployment.
                </div>
                <table class="kpi-table" id="kpi-beats-table">
                  <thead>
                    <tr>
                      <th style="width:48%">{{ $group->criteria_label }}</th>
                      <th>{{ $group->target_label }}</th>
                      <th>Scored</th>
                      <th>Per %</th>
                      <th>Merit</th>
                    </tr>
                  </thead>
                  <tbody id="kpi-beats-body"></tbody>
                </table>
              @elseif ($group->subItems->isEmpty())
                <p class="muted fw-5 mb-0" style="font-size:13px;">No sub-items configured for this group.</p>
              @else
                <table class="kpi-table">
                  <thead>
                    <tr>
                      <th style="width:48%">{{ $group->criteria_label }}</th>
                      <th>{{ $group->target_label }}</th>
                      <th>Scored</th>
                      <th>Per %</th>
                      <th>Merit</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($group->subItems as $item)
                      <tr data-sub-item-id="{{ $item->id }}">
                        <td>{{ $item->criteria }}</td>
                        <td><input type="number" value="{{ $item->target }}" readonly></td>
                        <td><input type="number" name="scored[{{ $item->id }}]"></td>
                        <td class="per-cell">—</td>
                        <td class="merit-cell muted">—</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              @endif
            </div>
          </section>
        @empty
          <div class="muted fw-5" style="padding:32px;text-align:center;border:1px dashed var(--border-soft);border-radius:12px;">
            No KPI groups configured yet. Configure them under <a href="{{ route('kpi.settings') }}">KPI Settings</a> first.
          </div>
        @endforelse

        <!-- Comments -->
        <div class="form-card mt-4">
          <label class="field-label">Officer comments <span class="muted fw-5">(optional)</span></label>
          <textarea class="brand-input" name="comments" rows="3" placeholder="Note anything unusual about today's shift...">{{ old('comments') }}</textarea>
        </div>

        <!-- Sticky total bar -->
        <div class="kpi-total-bar">
          <div class="stat"><span class="lbl">Date</span><span class="val" style="font-size:14px;">{{ now()->isoFormat('ddd, D MMM Y') }}</span></div>
          <div class="stat"><span class="lbl">Groups</span><span class="val">{{ $groups->count() }}</span></div>
          <div class="stat"><span class="lbl">Sub-items</span><span class="val">{{ $groups->sum(fn ($g) => $g->subItems->count()) }}</span></div>
          <div class="stat"><span class="lbl">Total weight</span><span class="val">{{ $groups->sum('weight') }}%</span></div>
          <div class="stat"><span class="lbl">Total merit</span><span class="val" id="kpi-total-merit" style="color:var(--brand-success);">— / 100</span></div>
          <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-outline-primary"><i class="bi bi-eye me-2"></i>Preview</button>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-lg me-2"></i>Submit
            </button>
          </div>
        </div>

        </form>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
  <script>
    // Per-row Per % colouring.
    function colourPer(cell, pct){
      cell.textContent = pct.toFixed(1) + '%';
      cell.classList.remove('per-green','per-amber','per-red');
      if (pct >= 80) cell.classList.add('per-green');
      else if (pct >= 50) cell.classList.add('per-amber');
      else cell.classList.add('per-red');
    }

    // Merit is system-generated: per line = weight × (scored ÷ Σ targets in the group).
    // Recompute the whole group a row belongs to, then refresh the grand total.
    function recalc(row){
      const section = row.closest('.kpi-group');
      if (!section) return;
      const weight = +section.dataset.weight || 0;
      const rows = section.querySelectorAll('tbody tr');

      let targetSum = 0;
      rows.forEach(r => { targetSum += +r.cells[1]?.querySelector('input')?.value || 0; });

      rows.forEach(r => {
        const tgt = +r.cells[1]?.querySelector('input')?.value || 0;
        const got = +r.cells[2]?.querySelector('input')?.value || 0;
        const perCell = r.cells[3];
        if (perCell) colourPer(perCell, tgt ? Math.min((got/tgt)*100, 200) : 0);

        const meritCell = r.cells[4];
        if (meritCell) {
          const merit = targetSum > 0 ? weight * (got / targetSum) : 0;
          meritCell.textContent = merit.toFixed(1);
          meritCell.classList.toggle('muted', merit === 0);
        }
      });

      updateTotal();
    }

    // Sum every merit cell on the page → "X.X / <total weight>".
    function updateTotal(){
      const el = document.getElementById('kpi-total-merit');
      if (!el) return;
      let total = 0, weight = 0;
      document.querySelectorAll('.kpi-group').forEach(s => {
        weight += +s.dataset.weight || 0;
        s.querySelectorAll('.merit-cell').forEach(c => { total += +c.textContent || 0; });
      });
      el.textContent = total.toFixed(1) + ' / ' + weight;
    }

    function bindRow(tr){
      tr.querySelectorAll('input[type=number],input').forEach(i => i.addEventListener('input', () => recalc(tr)));
    }
    document.querySelectorAll('.kpi-table tbody tr').forEach(bindRow);

    // ---- Deployment selector: Location -> Building -> (optional) Place ----
    const BUILDINGS = @json($buildings);
    const PLACES = @json($places);
    const DEPLOYMENTS = @json($deployments);

    const locSel   = document.getElementById('kpi-loc');
    const buildSel = document.getElementById('kpi-building');
    const placeSel = document.getElementById('kpi-place');
    const dateInp  = document.getElementById('kpi-date');
    const summary  = document.getElementById('kpi-deploy-summary');
    const beatsBody = document.getElementById('kpi-beats-body');

    // Mirror the selectors into the hidden form inputs so they submit with the scorecard.
    const locInput   = document.getElementById('kpi-loc-input');
    const buildInput = document.getElementById('kpi-building-input');
    const dateInput  = document.getElementById('kpi-date-input');
    function syncHidden(){
      locInput.value   = locSel.value;
      buildInput.value = buildSel.value;
      dateInput.value  = dateInp ? dateInp.value : '';
    }
    syncHidden();

    function fill(select, items, placeholder, allowAll){
      select.innerHTML = '';
      const ph = document.createElement('option');
      ph.value = '';
      ph.textContent = placeholder;
      if (!allowAll) ph.disabled = true;
      ph.selected = true;
      select.appendChild(ph);
      items.forEach(it => {
        const opt = document.createElement('option');
        opt.value = it.id;
        opt.textContent = it.name;
        select.appendChild(opt);
      });
      select.disabled = items.length === 0 && !allowAll;
    }

    function loadBuildings(){
      const items = BUILDINGS.filter(b => String(b.location_id) === String(locSel.value));
      fill(buildSel, items, 'Select a building...', false);
    }
    function loadPlaces(){
      const items = PLACES.filter(p => String(p.building_id) === String(buildSel.value));
      fill(placeSel, items, 'All places in building', true);
      placeSel.disabled = false; // optional filter is always usable
    }

    function renderDeployment(){
      const buildingId = buildSel.value;
      const placeId = placeSel.value;
      const date = dateInp ? dateInp.value : '';

      // --- Deployment summary for the building on the chosen date ---
      if (!buildingId) {
        summary.textContent = 'Select a building and date to load the deployment.';
      } else {
        const matches = DEPLOYMENTS.filter(d => String(d.building_id) === String(buildingId) && d.date === date);
        if (!matches.length) {
          summary.textContent = 'No deployment recorded for this building on this date.';
        } else {
          summary.innerHTML = matches.map(d =>
            `<div><i class="bi bi-shield-check me-2"></i><strong>${esc(d.company || '—')}</strong>`
            + ` · ${esc(d.shift || '—')} shift · ${d.guards} guard${d.guards == 1 ? '' : 's'}`
            + ` · ${esc(d.officer || '—')} <span class="muted">(${d.start}–${d.end})</span></div>`
          ).join('');
        }
      }

      // --- Beats = places under the building (or just the chosen place) ---
      beatsBody.innerHTML = '';
      if (!buildingId) return;
      let beats = PLACES.filter(p => String(p.building_id) === String(buildingId));
      if (placeId) beats = beats.filter(p => String(p.id) === String(placeId));

      beats.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td></td>`
          + `<td><input type="number" value="${p.estimated_guards ?? 0}" readonly></td>`
          + `<td><input type="number" name="beat_scored[${p.id}]"></td>`
          + `<td class="per-cell">—</td>`
          + `<td class="merit-cell muted">—</td>`;
        tr.cells[0].textContent = p.name; // safe assignment
        beatsBody.appendChild(tr);
        bindRow(tr);
      });
    }

    function esc(s){ const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    locSel.addEventListener('change', () => { loadBuildings(); placeSel.innerHTML = '<option value="">All places in building</option>'; placeSel.disabled = true; syncHidden(); renderDeployment(); });
    buildSel.addEventListener('change', () => { loadPlaces(); syncHidden(); renderDeployment(); });
    placeSel.addEventListener('change', renderDeployment);
    if (dateInp) dateInp.addEventListener('change', () => { syncHidden(); renderDeployment(); });
  </script>
</body>
</html>
