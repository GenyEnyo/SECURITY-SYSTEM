@extends('layouts.app')

@section('title', 'KPI Reports · M Dashboard')
@section('page-title', 'KPI Reports')
@section('crumbs')
  <li class="breadcrumb-item">Reports</li>
  <li class="breadcrumb-item active">KPI Reports</li>
@endsection

@section('content')
  @php
    $rangeQuery = fn ($r) => http_build_query(array_merge(['range' => $r], array_filter($filters)));
    $best  = $agg['best'];
    $worst = $agg['worst'];
  @endphp

  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
      <h4 class="mb-0">KPI Reports</h4>
      <p class="text-muted mb-0">Performance trends · {{ $rangeLabel }}</p>
    </div>
    <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
      <input type="hidden" name="range" value="{{ $range }}">
      <div>
        <label class="form-label">Location</label>
        <select name="location_id" class="form-select" style="width:170px;">
          <option value="">All locations</option>
          @foreach ($locations as $location)
            <option value="{{ $location->id }}" @selected(($filters['location_id'] ?? '') == $location->id)>{{ $location->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="form-label">Building</label>
        <select name="building_id" class="form-select" style="width:170px;">
          <option value="">All buildings</option>
          @foreach ($buildings as $building)
            <option value="{{ $building->id }}" @selected(($filters['building_id'] ?? '') == $building->id)>{{ $building->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <button type="submit" class="btn btn-primary"><i class="ti ti-filter me-1"></i>Filter</button>
        <a href="{{ route('kpi.reports.monthly') }}" class="btn btn-outline-primary"><i class="ti ti-file-text me-1"></i>Monthly report</a>
      </div>
    </form>
  </div>

  <!-- Range buttons -->
  <div class="card">
    <div class="card-body">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted fw-semibold">View:</span>
        @foreach (['week' => 'Weekly', 'month' => 'Monthly', 'quarter' => 'Quarterly', 'ytd' => 'YTD'] as $key => $label)
          <a href="{{ route('kpi.reports.index') }}?{{ $rangeQuery($key) }}"
             class="btn btn-sm {{ $range === $key ? 'btn-primary' : 'btn-outline-primary' }}">{{ $label }}</a>
        @endforeach
      </div>
    </div>
  </div>

  <!-- Summary cards -->
  <div class="row">
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">{{ $agg['overall_pct'] }}%</h4>
              <p class="text-muted mb-0">Overall score</p>
            </div>
            <div class="avatar-md bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-chart-arrows-vertical"></i>
            </div>
          </div>
          <p class="text-muted mb-0 mt-2"><span class="badge bg-light text-muted">{{ $rangeLabel }}</span></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1" style="font-size:18px;line-height:1.2;">{{ $best['name'] ?? '—' }}</h4>
              <p class="text-muted mb-0">Best group</p>
            </div>
            <div class="avatar-md bg-success-subtle text-success rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-trophy"></i>
            </div>
          </div>
          <p class="mb-0 mt-2"><span class="badge bg-success-subtle text-success">{{ $best ? $best['attainment'] . '% avg' : 'No data' }}</span></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1" style="font-size:18px;line-height:1.2;">{{ $worst['name'] ?? '—' }}</h4>
              <p class="text-muted mb-0">Needs attention</p>
            </div>
            <div class="avatar-md bg-danger-subtle text-danger rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-alert-triangle"></i>
            </div>
          </div>
          <p class="mb-0 mt-2"><span class="badge bg-danger-subtle text-danger">{{ $worst ? $worst['attainment'] . '% avg' : 'No data' }}</span></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">{{ $agg['count'] }}</h4>
              <p class="text-muted mb-0">Scorecards filed</p>
            </div>
            <div class="avatar-md bg-warning-subtle text-warning rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-calendar-check"></i>
            </div>
          </div>
          <p class="text-muted mb-0 mt-2"><span class="badge bg-light text-muted">in {{ $rangeLabel }}</span></p>
        </div>
      </div>
    </div>
  </div>

  @if ($agg['count'] === 0)
    <div class="card">
      <div class="card-body text-center text-muted py-4">
        No scorecards found for {{ $rangeLabel }}@if(array_filter($filters)) with the selected filters @endif.
      </div>
    </div>
  @else
    <!-- Charts -->
    <div class="row">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
              <div class="fw-semibold" style="font-size:16px;">Average attainment by KPI group</div>
              <span class="text-muted fw-semibold" style="font-size:12px;">Target line at 80%</span>
            </div>
            <canvas id="groupBar" height="120"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card h-100">
          <div class="card-body">
            <div class="fw-semibold mb-3" style="font-size:16px;">6-period trend</div>
            <canvas id="trendLine" height="160"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Proposed vs actual deployment by location -->
    @if (count($deployment['labels']))
      <div class="card">
        <div class="card-body">
          <div class="fw-semibold mb-3" style="font-size:16px;">Proposed vs actual deployment by location <span class="text-muted" style="font-size:12px;">(guard-days)</span></div>
          <canvas id="deployBar" height="110"></canvas>
        </div>
      </div>
    @endif

    <!-- Group breakdown table -->
    <h4 class="mt-4 mb-2">Group breakdown</h4>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-centered mb-0">
            <thead class="table-light">
              <tr><th>KPI group</th><th>Weight</th><th>Avg attainment</th><th>Weighted points</th></tr>
            </thead>
            <tbody>
              @foreach ($agg['groups'] as $g)
                <tr>
                  <td class="fw-semibold">{{ $g['name'] }}</td>
                  <td>{{ $g['weight'] }}%</td>
                  <td>{{ $g['attainment'] }}%</td>
                  <td>{{ round($g['weight'] * $g['attainment'] / 100, 1) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
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
