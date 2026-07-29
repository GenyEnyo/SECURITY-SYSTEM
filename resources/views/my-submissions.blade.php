@extends('layouts.app')

@section('title', 'My submissions · M Dashboard')
@section('page-title', 'My submissions')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Records</a></li>
  <li class="breadcrumb-item active">My submissions</li>
@endsection

@section('content')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">My submissions</h4>
      <p class="text-muted mb-0">KPI scorecards and deployment logs you've filed</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <select class="form-select" style="width:160px;"><option>April 2026</option><option selected>May 2026</option><option>June 2026</option></select>
      <div class="btn-group">
        <button class="btn btn-primary btn-sm" id="view-cal"><i class="ti ti-calendar me-1"></i>Calendar</button>
        <button class="btn btn-outline-primary btn-sm" id="view-list"><i class="ti ti-list me-1"></i>List</button>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">22 / 22</h4>
              <p class="text-muted mb-0">KPI scorecards</p>
            </div>
            <div class="avatar-md bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-clipboard-check"></i>
            </div>
          </div>
          <small class="badge bg-success-subtle text-success">100% compliance</small>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">22 / 22</h4>
              <p class="text-muted mb-0">Deployment logs</p>
            </div>
            <div class="avatar-md bg-success-subtle text-success rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-clipboard-list"></i>
            </div>
          </div>
          <small class="badge bg-success-subtle text-success">100% compliance</small>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">89.4%</h4>
              <p class="text-muted mb-0">Avg KPI score</p>
            </div>
            <div class="avatar-md bg-info-subtle text-info rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-chart-arrows"></i>
            </div>
          </div>
          <small class="badge bg-success-subtle text-success">+2.1 vs Apr</small>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">3</h4>
              <p class="text-muted mb-0">Incidents filed</p>
            </div>
            <div class="avatar-md bg-warning-subtle text-warning rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-alert-triangle"></i>
            </div>
          </div>
          <small class="badge bg-secondary-subtle text-secondary">1 still open</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Legend -->
  <div class="legend mb-3">
    <span><span class="mark" style="background:var(--bs-primary);"></span> KPI scorecard</span>
    <span><span class="mark" style="background:var(--bs-success);"></span> Deployment log</span>
    <span><span class="mark" style="background:var(--bs-warning);"></span> Incident reported</span>
    <span class="text-muted">• % shows the day's KPI score</span>
  </div>

  <!-- Calendar view -->
  <div id="cal-view">
    <div class="cal-head">
      <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
    </div>
    <div class="cal-grid" id="cal-grid"></div>
  </div>

  <!-- List view -->
  <div id="list-view" style="display:none;" class="mt-3">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-centered mb-0">
            <thead class="table-light"><tr><th>Date</th><th>Day</th><th>KPI</th><th>Deployment</th><th>Incidents</th><th>KPI score</th><th class="text-end">Actions</th></tr></thead>
            <tbody id="list-tbody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('head')
  <style>
    .cal-grid { display:grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
    .cal-day {
      background: var(--bs-card-bg, #fff); border:1px solid var(--bs-border-color); border-radius:8px;
      padding: 8px; min-height: 78px;
      display:flex; flex-direction:column; gap:4px;
      position: relative;
    }
    .cal-day.outside { opacity:.35; }
    .cal-day .dnum { font-size:11px; font-weight:700; color: var(--bs-secondary-color); }
    .cal-day .marks { display:flex; gap:3px; flex-wrap:wrap; }
    .cal-day .mark {
      width:8px; height:8px; border-radius:50%;
    }
    .cal-day .pct {
      position:absolute; bottom:6px; right:8px;
      font-size:10px; font-weight:700; color: var(--bs-secondary-color);
    }
    .cal-head {
      display:grid; grid-template-columns: repeat(7, 1fr); gap: 6px;
      font-size: 11px; font-weight: 700; color: var(--bs-secondary-color);
      text-transform: uppercase; letter-spacing: .04em;
      padding: 0 4px 6px;
    }
    .legend { display:flex; gap:14px; font-size:12px; align-items:center; }
    .legend .mark { width:10px; height:10px; border-radius:50%; display:inline-block; }
  </style>
@endpush

@push('scripts')
  <script>
    // May 2026 starts on Friday. 31 days. We'll render 6 weeks.
    const grid = document.getElementById('cal-grid');
    const listTb = document.getElementById('list-tbody');
    // Mon=0 in our layout
    const firstWeekday = 4; // Mon=0..Sun=6 -> May 1 2026 is Friday
    const totalDays = 31;
    const cells = 42; // 6 weeks
    const today = 18;
    const dayNames = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    for (let i = 0; i < cells; i++) {
      const dnum = i - firstWeekday + 1;
      const inMonth = dnum >= 1 && dnum <= totalDays;
      const div = document.createElement('div');
      div.className = 'cal-day' + (inMonth ? '' : ' outside');
      const isFuture = inMonth && dnum > today;
      const isWeekend = i % 7 >= 5;
      let marks = '';
      let pct = '';
      if (inMonth && !isFuture && !isWeekend) {
        marks += '<span class="mark" style="background:var(--bs-primary);"></span>';
        marks += '<span class="mark" style="background:var(--bs-success);"></span>';
        const score = 80 + Math.round(Math.random()*18);
        pct = score + '%';
        if ([3, 11, 18].includes(dnum)) marks += '<span class="mark" style="background:var(--bs-warning);"></span>';
        // build list row
        listTb.innerHTML += `<tr>
          <td class="fw-semibold">${String(dnum).padStart(2,'0')} May 2026</td>
          <td>${dayNames[i%7]}</td>
          <td><span class="badge bg-success-subtle text-success">Filed</span></td>
          <td><span class="badge bg-success-subtle text-success">Filed</span></td>
          <td>${[3,11,18].includes(dnum) ? '<span class="badge bg-info-subtle text-info">1</span>' : '<span class="text-muted">0</span>'}</td>
          <td><strong>${score}%</strong></td>
          <td class="text-end"><button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-eye"></i></button> <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-download"></i></button></td>
        </tr>`;
      } else if (inMonth && !isFuture && isWeekend) {
        div.style.background = 'var(--bs-tertiary-bg)';
      }
      div.innerHTML = `<div class="dnum">${inMonth ? dnum : ''}</div>
                       <div class="marks">${marks}</div>
                       <div class="pct">${pct}</div>`;
      if (dnum === today) {
        div.style.border = '2px solid var(--bs-primary)';
        div.style.background = 'var(--bs-primary-bg-subtle)';
      }
      grid.appendChild(div);
    }
    // Toggle views
    document.getElementById('view-cal').addEventListener('click', () => {
      document.getElementById('cal-view').style.display = '';
      document.getElementById('list-view').style.display = 'none';
      document.getElementById('view-cal').classList.add('btn-primary'); document.getElementById('view-cal').classList.remove('btn-outline-primary');
      document.getElementById('view-list').classList.remove('btn-primary'); document.getElementById('view-list').classList.add('btn-outline-primary');
    });
    document.getElementById('view-list').addEventListener('click', () => {
      document.getElementById('cal-view').style.display = 'none';
      document.getElementById('list-view').style.display = '';
      document.getElementById('view-list').classList.add('btn-primary'); document.getElementById('view-list').classList.remove('btn-outline-primary');
      document.getElementById('view-cal').classList.remove('btn-primary'); document.getElementById('view-cal').classList.add('btn-outline-primary');
    });
  </script>
@endpush
