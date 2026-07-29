@extends('layouts.app')

@section('title', 'Deployment compliance · M Dashboard')
@section('page-title', 'Deployment compliance')
@section('crumbs')
  <li class="breadcrumb-item">Reports</li>
  <li class="breadcrumb-item active">Deployment compliance</li>
@endsection

@push('head')
  <style>
    /* per-cell percentage pills (originally in extras.css, which the new layout no longer loads) */
    .per-cell { font-weight: 600; border-radius: 6px; padding: 2px 8px; display: inline-block; text-align: center; }
    .per-green { background: rgba(61,179,110,.18); color:#1f7a45; }
    .per-amber { background: rgba(255,169,31,.22); color:#8a5500; }
    .per-red   { background: rgba(252,51,32,.16);  color:#a8210d; }
  </style>
@endpush

@section('content')
  @php $pctCls = fn ($p) => $p >= 95 ? 'per-green' : ($p >= 80 ? 'per-amber' : 'per-red'); @endphp

  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
      <h4 class="mb-0">Deployment Compliance — {{ $monthLabel }}</h4>
      <p class="text-muted mb-0">Contracted guards vs. deployed guards · day-by-day</p>
    </div>
    <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
      <div>
        <label class="form-label">Month</label>
        <input type="month" name="month" value="{{ $month }}" class="form-control" style="width:160px;">
      </div>
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
      </div>
    </form>
  </div>

  <!-- Summary metric cards -->
  <div class="row">
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">{{ number_format($contracted) }}</h4>
              <p class="text-muted mb-0">Contracted</p>
            </div>
            <div class="avatar-md bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-calendar-week"></i>
            </div>
          </div>
          <p class="text-muted mb-0 mt-2"><span class="badge bg-light text-muted">guard-days</span></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">{{ number_format($deployed) }}</h4>
              <p class="text-muted mb-0">Deployed</p>
            </div>
            <div class="avatar-md bg-success-subtle text-success rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-checkbox"></i>
            </div>
          </div>
          <p class="mb-0 mt-2"><span class="badge bg-success-subtle text-success">guard-days</span></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">{{ $pct }}%</h4>
              <p class="text-muted mb-0">Deployment %</p>
            </div>
            <div class="avatar-md bg-warning-subtle text-warning rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-chart-arrows-vertical"></i>
            </div>
          </div>
          <p class="mb-0 mt-2"><span class="badge {{ $pct >= 95 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $pct >= 95 ? 'on target' : (100 - $pct) . '% shortfall' }}</span></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1 {{ $shortfall > 0 ? 'text-danger' : '' }}">{{ number_format($shortfall) }}</h4>
              <p class="text-muted mb-0">Shortfall</p>
            </div>
            <div class="avatar-md bg-danger-subtle text-danger rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-circle-minus"></i>
            </div>
          </div>
          <p class="text-muted mb-0 mt-2"><span class="badge bg-light text-muted">guard-days</span></p>
        </div>
      </div>
    </div>
  </div>

  @if ($days->isEmpty())
    <div class="card">
      <div class="card-body text-center text-muted py-4">
        No deployment scorecards found for {{ $monthLabel }}@if(array_filter($filters)) with the selected filters @endif.
      </div>
    </div>
  @else
    <!-- By beat -->
    <h4 class="mt-4 mb-2">By beat (month aggregate)</h4>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-centered mb-0">
            <thead class="table-light">
              <tr><th>Beat</th><th style="width:140px">Contracted</th><th style="width:140px">Deployed</th><th style="width:140px">Deployment %</th></tr>
            </thead>
            <tbody>
              @foreach ($beats as $b)
                <tr>
                  <td class="fw-semibold">{{ $b['name'] }}</td>
                  <td>{{ number_format($b['contracted']) }}</td>
                  <td>{{ number_format($b['deployed']) }}</td>
                  <td><span class="per-cell {{ $pctCls($b['pct']) }}">{{ $b['pct'] }}%</span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Day-by-day -->
    <h4 class="mt-4 mb-2">Day-by-day deployment ({{ $monthLabel }})</h4>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-centered mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:110px">Day</th>
                <th>Location</th>
                <th>Building</th>
                <th style="width:120px">Contracted</th>
                <th style="width:120px">Deployed</th>
                <th style="width:120px">Deploy %</th>
                <th style="width:110px">Shortfall</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($days as $d)
                <tr>
                  <td class="fw-semibold">{{ $d['date']->format('d M') }}</td>
                  <td>{{ $d['location'] }}</td>
                  <td>{{ $d['building'] }}</td>
                  <td>{{ number_format($d['contracted']) }}</td>
                  <td class="fw-semibold">{{ number_format($d['deployed']) }}</td>
                  <td><span class="per-cell {{ $pctCls($d['pct']) }}">{{ $d['pct'] }}%</span></td>
                  <td>
                    @if ($d['shortfall'] > 0)
                      <span class="text-danger fw-bold">-{{ number_format($d['shortfall']) }}</span>
                    @else
                      <span class="text-success fw-bold">0</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endif
@endsection
