@extends('layouts.app')

@section('title', 'KPI scorecard · M Dashboard')
@section('page-title', 'KPI Scorecard — Daily entry')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Daily entry</a></li>
  <li class="breadcrumb-item active">KPI scorecard</li>
@endsection

@push('head')
  <style>
    /* per-row score pills (originally in extras.css, which the new layout no longer loads) */
    .kpi-table .per-cell { font-weight: 600; border-radius: 6px; text-align: center; }
    .per-green { background: rgba(61,179,110,.18); color:#1f7a45; }
    .per-amber { background: rgba(255,169,31,.22); color:#8a5500; }
    .per-red   { background: rgba(252,51,32,.16);  color:#a8210d; }
    .kpi-totalbar { position: sticky; bottom: 0; z-index: 5; }
  </style>
@endpush

@section('content')
  <div class="d-flex justify-content-end mb-3">
    <input type="date" class="form-control" id="kpi-date" value="{{ now()->toDateString() }}" style="width:180px;">
  </div>

  @if (session('status'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
      <i class="ti ti-circle-check me-2 fs-lg"></i>{{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger" role="alert">
      @foreach ($errors->all() as $message)
        <div><i class="ti ti-alert-triangle me-2"></i>{{ $message }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('entries.store') }}" class="pb-5">
    @csrf
    <input type="hidden" name="location_id" id="kpi-loc-input">
    <input type="hidden" name="building_id" id="kpi-building-input">
    <input type="hidden" name="date" id="kpi-date-input">

    <!-- Deployment location selector -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-lg-4">
            <label class="form-label" for="kpi-loc">Location</label>
            <select id="kpi-loc" class="form-select">
              <option value="" disabled selected>Select a location...</option>
              @foreach ($locations as $location)
                <option value="{{ $location->id }}">{{ $location->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-4">
            <label class="form-label" for="kpi-building">Building</label>
            <select id="kpi-building" class="form-select" disabled>
              <option value="" disabled selected>Select a location first...</option>
            </select>
          </div>
          <div class="col-lg-4">
            <label class="form-label" for="kpi-place">Specific place <span class="text-muted fw-normal">(optional)</span></label>
            <select id="kpi-place" class="form-select" disabled>
              <option value="">All places in building</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- KPI groups as accordion -->
    <div class="accordion" id="kpiGroups">
      @forelse ($groups as $group)
        @php
          $isDeployment = strtolower(trim($group->name)) === 'deployment';
          $gIdx = $loop->index;
        @endphp
        <div class="accordion-item kpi-group" data-group data-weight="{{ $group->weight }}">
          <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#kpiG{{ $gIdx }}" aria-expanded="true" aria-controls="kpiG{{ $gIdx }}">
              <div>
                <span class="fw-semibold d-block">{{ $group->name }}</span>
                <small class="text-muted">
                  {{ $group->subItems->count() }} sub-item{{ $group->subItems->count() === 1 ? '' : 's' }} · weight {{ $group->weight }}%
                </small>
              </div>
            </button>
          </h2>
          <div id="kpiG{{ $gIdx }}" class="accordion-collapse collapse show">
            <div class="accordion-body">
              @if ($isDeployment)
                <div id="kpi-deploy-summary" class="text-muted mb-3 small">
                  Select a building and date to load the deployment.
                </div>
                <div class="table-responsive">
                  <table class="table table-sm kpi-table align-middle mb-0" id="kpi-beats-table">
                    <thead class="table-light">
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
                </div>
              @elseif ($group->subItems->isEmpty())
                <p class="text-muted mb-0 small">No sub-items configured for this group.</p>
              @else
                <div class="table-responsive">
                  <table class="table table-sm kpi-table align-middle mb-0">
                    <thead class="table-light">
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
                          <td><input type="number" class="form-control form-control-sm" style="max-width:110px" value="{{ $item->target }}" readonly></td>
                          <td><input type="number" class="form-control form-control-sm" style="max-width:110px" name="scored[{{ $item->id }}]"></td>
                          <td class="per-cell">—</td>
                          <td class="merit-cell muted">—</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="card">
          <div class="card-body text-center text-muted py-5">
            No KPI groups configured yet. Configure them under
            <a href="{{ route('kpi.settings') }}">KPI Settings</a> first.
          </div>
        </div>
      @endforelse
    </div>

    <!-- Comments -->
    <div class="card mt-3">
      <div class="card-body">
        <label class="form-label">Officer comments <span class="text-muted fw-normal">(optional)</span></label>
        <textarea class="form-control" name="comments" rows="3" placeholder="Note anything unusual about today's shift...">{{ old('comments') }}</textarea>
      </div>
    </div>

    <!-- Sticky total bar -->
    <div class="card kpi-totalbar mt-3 shadow">
      <div class="card-body d-flex flex-wrap align-items-center gap-4">
        <div><div class="text-muted small">Date</div><div class="fw-semibold">{{ now()->isoFormat('ddd, D MMM Y') }}</div></div>
        <div><div class="text-muted small">Groups</div><div class="fw-semibold">{{ $groups->count() }}</div></div>
        <div><div class="text-muted small">Sub-items</div><div class="fw-semibold">{{ $groups->sum(fn ($g) => $g->subItems->count()) }}</div></div>
        <div><div class="text-muted small">Total weight</div><div class="fw-semibold">{{ $groups->sum('weight') }}%</div></div>
        <div><div class="text-muted small">Total merit</div><div class="fw-semibold text-success" id="kpi-total-merit">— / 100</div></div>
        <div class="ms-auto d-flex gap-2">
          <button type="button" class="btn btn-outline-primary"><i class="ti ti-eye me-1"></i>Preview</button>
          <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Submit</button>
        </div>
      </div>
    </div>

  </form>
@endsection

@push('scripts')
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
      placeSel.disabled = false;
    }

    function renderDeployment(){
      const buildingId = buildSel.value;
      const placeId = placeSel.value;
      const date = dateInp ? dateInp.value : '';

      if (!buildingId) {
        summary.textContent = 'Select a building and date to load the deployment.';
      } else {
        const matches = DEPLOYMENTS.filter(d => String(d.building_id) === String(buildingId) && d.date === date);
        if (!matches.length) {
          summary.textContent = 'No deployment recorded for this building on this date.';
        } else {
          summary.innerHTML = matches.map(d =>
            `<div><i class="ti ti-shield-check me-2"></i><strong>${esc(d.company || '—')}</strong>`
            + ` · ${esc(d.shift || '—')} shift · ${d.guards} guard${d.guards == 1 ? '' : 's'}`
            + ` · ${esc(d.officer || '—')} <span class="text-muted">(${d.start}–${d.end})</span></div>`
          ).join('');
        }
      }

      beatsBody.innerHTML = '';
      if (!buildingId) return;
      let beats = PLACES.filter(p => String(p.building_id) === String(buildingId));
      if (placeId) beats = beats.filter(p => String(p.id) === String(placeId));

      beats.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td></td>`
          + `<td><input type="number" class="form-control form-control-sm" style="max-width:110px" value="${p.estimated_guards ?? 0}" readonly></td>`
          + `<td><input type="number" class="form-control form-control-sm" style="max-width:110px" name="beat_scored[${p.id}]"></td>`
          + `<td class="per-cell">—</td>`
          + `<td class="merit-cell muted">—</td>`;
        tr.cells[0].textContent = p.name;
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
@endpush
