@extends('layouts.app')

@section('title', 'KPI Reports · M Dashboard')
@section('page', 'kpi-reports')
@section('crumbs', '[{"label":"Reports"},{"label":"KPI Reports"}]')

@section('content')
  @php
    $rangeQuery = fn ($r) => http_build_query(array_merge(['range' => $r], array_filter($filters)));
    $best  = $agg['best'];
    $worst = $agg['worst'];
  @endphp

  <div class="page-head">
    <div>
      <h1 class="page-title">KPI Reports</h1>
      <p class="page-subtitle">Performance trends · {{ $rangeLabel }}</p>
    </div>
    <div class="actions">
      <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
        <input type="hidden" name="range" value="{{ $range }}">
        <select name="location_id" class="brand-select" style="width:170px;">
          <option value="">All locations</option>
          @foreach ($locations as $location)
            <option value="{{ $location->id }}" @selected(($filters['location_id'] ?? '') == $location->id)>{{ $location->name }}</option>
          @endforeach
        </select>
        <select name="building_id" class="brand-select" style="width:170px;">
          <option value="">All buildings</option>
          @foreach ($buildings as $building)
            <option value="{{ $building->id }}" @selected(($filters['building_id'] ?? '') == $building->id)>{{ $building->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
        <a href="{{ route('kpi.reports.monthly') }}" class="btn btn-outline-primary"><i class="bi bi-file-earmark-pdf me-1"></i>Monthly report</a>
      </form>
    </div>
  </div>

  <!-- Range buttons -->
  <div class="toolbar">
    <div class="d-flex gap-2 align-items-center" style="font-size:13px;">
      <span class="muted fw-7">View:</span>
      @foreach (['week' => 'Weekly', 'month' => 'Monthly', 'quarter' => 'Quarterly', 'ytd' => 'YTD'] as $key => $label)
        <a href="{{ route('kpi.reports.index') }}?{{ $rangeQuery($key) }}"
           class="btn btn-sm {{ $range === $key ? 'btn-primary' : 'btn-outline-primary' }}">{{ $label }}</a>
      @endforeach
    </div>
  </div>

  <!-- Summary cards -->
  <div class="row g-3">
    <div class="col-md-3"><div class="metric-card">
      <div class="icon-wrap"><i class="bi bi-graph-up-arrow"></i></div>
      <div class="label">Overall score</div>
      <div class="value">{{ $agg['overall_pct'] }}%</div>
      <div class="delta muted">{{ $rangeLabel }}</div>
    </div></div>
    <div class="col-md-3"><div class="metric-card">
      <div class="icon-wrap" style="background:rgba(61,179,110,.12);color:var(--brand-success);"><i class="bi bi-trophy"></i></div>
      <div class="label">Best group</div>
      <div class="value" style="font-size:18px;line-height:1.2;">{{ $best['name'] ?? '—' }}</div>
      <div class="delta up">{{ $best ? $best['attainment'] . '% avg' : 'No data' }}</div>
    </div></div>
    <div class="col-md-3"><div class="metric-card">
      <div class="icon-wrap" style="background:rgba(252,51,32,.12);color:var(--brand-danger);"><i class="bi bi-exclamation-triangle"></i></div>
      <div class="label">Needs attention</div>
      <div class="value" style="font-size:18px;line-height:1.2;">{{ $worst['name'] ?? '—' }}</div>
      <div class="delta down">{{ $worst ? $worst['attainment'] . '% avg' : 'No data' }}</div>
    </div></div>
    <div class="col-md-3"><div class="metric-card">
      <div class="icon-wrap" style="background:rgba(255,169,31,.18);color:var(--brand-warning);"><i class="bi bi-calendar-check"></i></div>
      <div class="label">Scorecards filed</div>
      <div class="value">{{ $agg['count'] }}</div>
      <div class="delta muted">in {{ $rangeLabel }}</div>
    </div></div>
  </div>

  @if ($agg['count'] === 0)
    <div class="shadow-card p-4 mt-3 muted fw-5" style="text-align:center;">
      No scorecards found for {{ $rangeLabel }}@if(array_filter($filters)) with the selected filters @endif.
    </div>
  @else
    <!-- Charts -->
    <div class="row g-3 mt-1">
      <div class="col-lg-7">
        <div class="shadow-card p-4">
          <div class="d-flex justify-content-between mb-3">
            <div class="fw-7" style="font-size:16px;">Average attainment by KPI group</div>
            <span class="muted fw-7" style="font-size:12px;">Target line at 80%</span>
          </div>
          <canvas id="groupBar" height="120"></canvas>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="shadow-card p-4 h-100">
          <div class="fw-7 mb-3" style="font-size:16px;">6-period trend</div>
          <canvas id="trendLine" height="160"></canvas>
        </div>
      </div>
    </div>

    <!-- Proposed vs actual deployment by location -->
    @if (count($deployment['labels']))
      <div class="shadow-card p-4 mt-3">
        <div class="fw-7 mb-3" style="font-size:16px;">Proposed vs actual deployment by location <span class="muted fw-5" style="font-size:12px;">(guard-days)</span></div>
        <canvas id="deployBar" height="110"></canvas>
      </div>
    @endif

    <!-- Group breakdown table -->
    <div class="section-divider">Group breakdown</div>
    <table class="brand-table">
      <thead>
        <tr><th>KPI group</th><th>Weight</th><th>Avg attainment</th><th>Weighted points</th></tr>
      </thead>
      <tbody>
        @foreach ($agg['groups'] as $g)
          <tr>
            <td class="fw-7">{{ $g['name'] }}</td>
            <td>{{ $g['weight'] }}%</td>
            <td>{{ $g['attainment'] }}%</td>
            <td>{{ round($g['weight'] * $g['attainment'] / 100, 1) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection

@push('scripts')
  @if ($agg['count'] > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
      const BRAND = '#081f39', AMBER = '#FFA91F', GREEN = '#3DB36E', DANGER = '#FC3320';
      const groups     = @json($agg['groups']);
      const trend      = @json($trend);
      const deployment = @json($deployment);

      // Bar: average attainment by group, coloured by 80% target.
      new Chart(document.getElementById('groupBar'), {
        type: 'bar',
        data: {
          labels: groups.map(g => g.name),
          datasets: [{
            label: 'Avg attainment %',
            data: groups.map(g => g.attainment),
            backgroundColor: groups.map(g => g.attainment >= 80 ? GREEN : g.attainment >= 50 ? AMBER : DANGER),
            borderRadius: 4,
          }],
        },
        options: {
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } } },
        },
      });

      // Line: overall % over the last 6 periods.
      new Chart(document.getElementById('trendLine'), {
        type: 'line',
        data: {
          labels: trend.labels,
          datasets: [{
            label: 'Overall %',
            data: trend.values,
            borderColor: BRAND,
            backgroundColor: 'rgba(8,31,57,.08)',
            fill: true,
            tension: 0.3,
            pointRadius: 4,
          }],
        },
        options: {
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } } },
        },
      });

      // Grouped bar: proposed vs actual deployment guard-days, per location.
      if (document.getElementById('deployBar') && deployment.labels.length) {
        new Chart(document.getElementById('deployBar'), {
          type: 'bar',
          data: {
            labels: deployment.labels,
            datasets: [
              { label: 'Proposed', data: deployment.proposed, backgroundColor: BRAND, borderRadius: 4 },
              { label: 'Actual',   data: deployment.actual,   backgroundColor: GREEN, borderRadius: 4 },
            ],
          },
          options: {
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } },
          },
        });
      }
    </script>
  @endif
@endpush
