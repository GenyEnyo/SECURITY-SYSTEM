@extends('layouts.app')

@section('title', 'Deployment compliance · M Dashboard')
@section('page', 'kpi-compliance')
@section('crumbs', '[{"label":"Reports"},{"label":"Deployment compliance"}]')

@section('content')
  @php $pctCls = fn ($p) => $p >= 95 ? 'per-green' : ($p >= 80 ? 'per-amber' : 'per-red'); @endphp

  <div class="page-head">
    <div>
      <h1 class="page-title">Deployment Compliance — {{ $monthLabel }}</h1>
      <p class="page-subtitle">Contracted guards vs. deployed guards · day-by-day</p>
    </div>
    <div class="actions">
      <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
        <input type="month" name="month" value="{{ $month }}" class="brand-input" style="width:160px;">
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
      </form>
    </div>
  </div>

  <!-- Summary metric cards -->
  <div class="row g-3">
    <div class="col-md-3"><div class="metric-card">
      <div class="icon-wrap"><i class="bi bi-calendar-week"></i></div>
      <div class="label">Contracted</div>
      <div class="value">{{ number_format($contracted) }}</div>
      <div class="delta muted">guard-days</div>
    </div></div>
    <div class="col-md-3"><div class="metric-card">
      <div class="icon-wrap" style="background:rgba(61,179,110,.12);color:var(--brand-success);"><i class="bi bi-check2-square"></i></div>
      <div class="label">Deployed</div>
      <div class="value">{{ number_format($deployed) }}</div>
      <div class="delta up">guard-days</div>
    </div></div>
    <div class="col-md-3"><div class="metric-card">
      <div class="icon-wrap" style="background:rgba(255,169,31,.18);color:var(--brand-warning);"><i class="bi bi-graph-up-arrow"></i></div>
      <div class="label">Deployment %</div>
      <div class="value">{{ $pct }}%</div>
      <div class="delta {{ $pct >= 95 ? 'up' : 'down' }}">{{ $pct >= 95 ? 'on target' : (100 - $pct) . '% shortfall' }}</div>
    </div></div>
    <div class="col-md-3"><div class="metric-card">
      <div class="icon-wrap" style="background:rgba(252,51,32,.12);color:var(--brand-danger);"><i class="bi bi-dash-circle"></i></div>
      <div class="label">Shortfall</div>
      <div class="value" style="{{ $shortfall > 0 ? 'color:var(--brand-danger);' : '' }}">{{ number_format($shortfall) }}</div>
      <div class="delta muted">guard-days</div>
    </div></div>
  </div>

  @if ($days->isEmpty())
    <div class="shadow-card p-4 mt-3 muted fw-5" style="text-align:center;">
      No deployment scorecards found for {{ $monthLabel }}@if(array_filter($filters)) with the selected filters @endif.
    </div>
  @else
    <!-- By beat -->
    <div class="section-divider">By beat (month aggregate)</div>
    <table class="brand-table">
      <thead>
        <tr><th>Beat</th><th style="width:140px">Contracted</th><th style="width:140px">Deployed</th><th style="width:140px">Deployment %</th></tr>
      </thead>
      <tbody>
        @foreach ($beats as $b)
          <tr>
            <td class="fw-7">{{ $b['name'] }}</td>
            <td>{{ number_format($b['contracted']) }}</td>
            <td>{{ number_format($b['deployed']) }}</td>
            <td><span class="per-cell {{ $pctCls($b['pct']) }}">{{ $b['pct'] }}%</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Day-by-day -->
    <div class="section-divider">Day-by-day deployment ({{ $monthLabel }})</div>
    <div class="shadow-card p-3" style="overflow-x:auto;">
      <table class="brand-table">
        <thead>
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
              <td class="fw-7">{{ $d['date']->format('d M') }}</td>
              <td>{{ $d['location'] }}</td>
              <td>{{ $d['building'] }}</td>
              <td>{{ number_format($d['contracted']) }}</td>
              <td class="fw-7">{{ number_format($d['deployed']) }}</td>
              <td><span class="per-cell {{ $pctCls($d['pct']) }}">{{ $d['pct'] }}%</span></td>
              <td>
                @if ($d['shortfall'] > 0)
                  <span style="color:var(--brand-danger);font-weight:700;">-{{ number_format($d['shortfall']) }}</span>
                @else
                  <span style="color:var(--brand-success);font-weight:700;">0</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
@endsection
