@extends('layouts.app')

@section('title', 'Dashboard · M Dashboard')
@section('page-title', 'Dashboard')
@section('crumbs')
  <li class="breadcrumb-item">Dashboards</li>
  <li class="breadcrumb-item active">Officer dashboard</li>
@endsection

@section('content')

  @php
    $statusColors = [
      'low' => 'success', 'resolved' => 'success', 'closed' => 'success',
      'reviewing' => 'info',
      'medium' => 'warning', 'reported' => 'warning',
      'urgent' => 'danger', 'escalated' => 'danger',
    ];
  @endphp

  <!-- KPI alert banner -->
  @if ($missingCards && $missingCards['missing'] > 0)
    <div class="alert alert-warning d-flex align-items-center justify-content-between" role="alert">
      <div class="d-flex align-items-center gap-3">
        <i class="ti ti-alert-octagon fs-22"></i>
        <div>
          <div class="fw-semibold fs-16">KPI scorecard not yet submitted</div>
          <div class="text-muted fw-medium">{{ $missingCards['missing'] }} of {{ $missingCards['buildings'] }} {{ Str::plural('building', $missingCards['buildings']) }} {{ $missingCards['missing'] === 1 ? 'has' : 'have' }} no KPI scorecard for today.</div>
        </div>
      </div>
      <a href="{{ route('entries.create') }}" class="btn btn-warning"><i class="ti ti-edit me-1"></i>Submit now</a>
    </div>
  @endif

  <!-- Metric cards -->
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">{{ $today['submitted'] ? $today['contracted'] : '—' }}</h4>
              <p class="text-muted mb-0">Contracted today</p>
            </div>
            <div class="avatar-md bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-users"></i>
            </div>
          </div>
          @if ($today['submitted'])
            <small class="badge bg-primary-subtle text-primary">Guards required</small>
          @else
            <small class="badge bg-secondary-subtle text-secondary">No scorecard yet today</small>
          @endif
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">{{ $today['submitted'] ? $today['deployed'] : '—' }}</h4>
              <p class="text-muted mb-0">Actual deployed</p>
            </div>
            <div class="avatar-md bg-success-subtle text-success rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-circle-check"></i>
            </div>
          </div>
          @if (! $today['submitted'])
            <small class="badge bg-secondary-subtle text-secondary">No scorecard yet today</small>
          @elseif ($today['shortfall'] > 0)
            <small class="badge bg-danger-subtle text-danger">{{ $today['shortfall'] }} short</small>
          @else
            <small class="badge bg-success-subtle text-success">Fully deployed</small>
          @endif
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">{{ $today['pct'] !== null ? $today['pct'].'%' : '—' }}</h4>
              <p class="text-muted mb-0">Deployment %</p>
            </div>
            <div class="avatar-md bg-warning-subtle text-warning rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-chart-arrows"></i>
            </div>
          </div>
          @if ($today['pct'] !== null && $yesterdayPct !== null)
            @php $pctDiff = round($today['pct'] - $yesterdayPct, 1); @endphp
            <small class="badge bg-{{ $pctDiff >= 0 ? 'success' : 'danger' }}-subtle text-{{ $pctDiff >= 0 ? 'success' : 'danger' }}">{{ $pctDiff > 0 ? '+' : '' }}{{ $pctDiff }} pts vs. yesterday</small>
          @else
            <small class="badge bg-{{ $today['pct'] !== null && $today['pct'] >= 100 ? 'success' : 'danger' }}-subtle text-{{ $today['pct'] !== null && $today['pct'] >= 100 ? 'success' : 'danger' }}">Target 100%</small>
          @endif
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">{{ $incidentsWeek }}</h4>
              <p class="text-muted mb-0">Incidents this week</p>
            </div>
            <div class="avatar-md bg-danger-subtle text-danger rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-shield-x"></i>
            </div>
          </div>
          @if ($incidentsDiff === 0)
            <small class="badge bg-secondary-subtle text-secondary">Same as last week</small>
          @else
            <small class="badge bg-{{ $incidentsDiff > 0 ? 'danger' : 'success' }}-subtle text-{{ $incidentsDiff > 0 ? 'danger' : 'success' }}">{{ $incidentsDiff > 0 ? '+' : '' }}{{ $incidentsDiff }} vs. last week</small>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Two-column: recent incidents + week chart -->
  <div class="row">
    <!-- Recent incidents -->
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h4 class="card-title mb-0">Recent incidents</h4>
          @can('View Incidents')
            <a href="{{ route('incidents.index') }}" class="fw-semibold fs-14">View all <i class="ti ti-arrow-right"></i></a>
          @endcan
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-centered mb-0">
              <thead class="table-light">
                <tr><th>Date</th><th>Type</th><th>Location</th><th>Severity</th><th>Status</th></tr>
              </thead>
              <tbody>
                @forelse ($recent as $incident)
                  @php $sc = $statusColors[strtolower($incident->status?->name ?? '')] ?? 'secondary'; @endphp
                  <tr>
                    <td>
                      @can('View Incidents')
                        <a href="{{ route('incidents.show', $incident) }}">{{ $incident->occurred_at->format('d/m/y') }}</a>
                      @else
                        {{ $incident->occurred_at->format('d/m/y') }}
                      @endcan
                    </td>
                    <td>{{ $incident->incidentType?->name }}</td>
                    <td>{{ $incident->location?->name }}</td>
                    <td><span class="badge" style="background:{{ $incident->severity?->color }}1f;color:{{ $incident->severity?->color }};">{{ $incident->severity?->name }}</span></td>
                    <td><span class="badge bg-{{ $sc }}-subtle text-{{ $sc }}">{{ $incident->status?->name }}</span></td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No incidents recorded.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Week chart -->
    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h4 class="card-title mb-0">Incidents this week</h4>
          <span class="text-muted fw-semibold fs-13">Mon — Sun</span>
        </div>
        <div class="card-body">
          <div style="display:flex;align-items:flex-end;gap:14px;height:200px;padding:10px 0;">
            @foreach ($week as $day)
              <div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;gap:8px;height:100%;">
                <div class="fw-semibold fs-12 {{ $day['future'] ? 'text-muted opacity-50' : '' }}">{{ $day['future'] ? '' : $day['count'] }}</div>
                <div class="{{ $day['critical'] ? 'bg-danger' : 'bg-primary' }}" style="width:100%;height:{{ $day['height'] }}%;border-radius:6px 6px 0 0;"></div>
                <div class="text-muted fw-semibold fs-12 {{ $day['future'] ? 'opacity-50' : '' }}">{{ $day['label'] }}</div>
              </div>
            @endforeach
          </div>
          <div class="d-flex gap-3 mt-3 fs-12">
            <div class="d-flex align-items-center gap-2"><span class="bg-primary" style="width:10px;height:10px;border-radius:2px;"></span> Incidents</div>
            <div class="d-flex align-items-center gap-2"><span class="bg-danger" style="width:10px;height:10px;border-radius:2px;"></span> Includes High or Urgent</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick actions row -->
  <h4 class="mt-4 mb-2">Quick actions</h4>
  <div class="row">
    @can('Report Incident')
      <div class="col-sm-6 col-lg-3">
        <a href="{{ route('incidents.create') }}" class="card text-decoration-none text-body">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="avatar-md bg-danger-subtle text-danger rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-shield-plus"></i>
            </div>
            <div>
              <div class="fw-semibold">Report incident</div>
              <div class="text-muted fw-medium fs-13">New entry form</div>
            </div>
          </div>
        </a>
      </div>
    @endcan
    @can('View Deployments')
      <div class="col-sm-6 col-lg-3">
        <a href="{{ route('deployments.picker') }}" class="card text-decoration-none text-body">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="avatar-md bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-clipboard-check"></i>
            </div>
            <div>
              <div class="fw-semibold">Deployment log</div>
              <div class="text-muted fw-medium fs-13">Guards by building</div>
            </div>
          </div>
        </a>
      </div>
    @endcan
    @can('Submit KPI Scorecard')
      <div class="col-sm-6 col-lg-3">
        <a href="{{ route('entries.create') }}" class="card text-decoration-none text-body">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="avatar-md bg-warning-subtle text-warning rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-stars"></i>
            </div>
            <div>
              <div class="fw-semibold">Submit KPI scorecard</div>
              <div class="text-muted fw-medium fs-13">Daily form</div>
            </div>
          </div>
        </a>
      </div>
    @endcan
    @can('View My Submissions')
      <div class="col-sm-6 col-lg-3">
        <a href="{{ route('submissions.index') }}" class="card text-decoration-none text-body">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="avatar-md bg-success-subtle text-success rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-history"></i>
            </div>
            <div>
              <div class="fw-semibold">My submissions</div>
              <div class="text-muted fw-medium fs-13">History &amp; exports</div>
            </div>
          </div>
        </a>
      </div>
    @endcan
  </div>

@endsection
