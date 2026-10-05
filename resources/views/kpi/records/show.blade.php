@extends('layouts.app')

@section('title', 'Scorecard · ' . $record->date->format('d M Y') . ' · M Dashboard')
@section('page-title', 'KPI Scorecard')
@section('crumbs')
  <li class="breadcrumb-item">Records</li>
  <li class="breadcrumb-item"><a href="/kpi/records">KPI Records</a></li>
  <li class="breadcrumb-item active">{{ $record->date->format('d M Y') }}</li>
@endsection

@section('content')
  @php
    $standardLines = $record->lines->whereNull('place_id');
    $beatLines     = $record->lines->whereNotNull('place_id');
  @endphp

  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="mb-0">{{ $record->location_name }} · {{ $record->building_name }}</h4>
      <p class="text-muted mb-0">Scorecard for {{ $record->date->format('d M Y') }}</p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-soft-primary" href="{{ route('records.index') }}">
        <i class="ti ti-arrow-left me-1"></i>Back
      </a>
      @can('Edit KPI Record')
      <a class="btn btn-warning" href="{{ route('records.edit', $record) }}">
        <i class="ti ti-pencil me-1"></i>Edit
      </a>
      @endcan
      @can('Delete KPI Record')
      <form action="{{ route('records.destroy', $record) }}" method="POST"
            onsubmit="return confirm('Delete this scorecard? This cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="ti ti-trash me-1"></i>Delete</button>
      </form>
      @endcan
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
      <i class="ti ti-circle-check me-2 fs-lg"></i>{{ session('status') }}
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <dl class="row g-3 mb-0">
        <dt class="col-sm-3 text-muted fw-semibold">Location</dt>
        <dd class="col-sm-9">{{ $record->location_name }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Building</dt>
        <dd class="col-sm-9">{{ $record->building_name }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Date</dt>
        <dd class="col-sm-9">{{ $record->date->format('d M Y') }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Comments</dt>
        <dd class="col-sm-9">{{ $record->comments ?: '—' }}</dd>
      </dl>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th>KPI group</th>
              <th>Criteria</th>
              <th style="width:110px">Target</th>
              <th style="width:110px">Scored</th>
              <th style="width:110px">Merit</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($standardLines as $line)
              <tr>
                <td>{{ $line->group?->name ?? '—' }}</td>
                <td class="fw-semibold">{{ $line->criteria }}</td>
                <td>{{ $line->target ?? '—' }}</td>
                <td>{{ $line->scored ?? '—' }}</td>
                <td>{{ $line->merit !== null ? number_format($line->merit, 1) : '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted py-4">No KPI lines recorded.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if ($beatLines->isNotEmpty())
    <h4 class="mt-4 mb-2">Deployment beats</h4>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-centered mb-0">
            <thead class="table-light">
              <tr>
                <th>Place</th>
                <th style="width:140px">Est. guards</th>
                <th style="width:110px">Scored</th>
                <th style="width:110px">Merit</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($beatLines as $line)
                <tr>
                  <td class="fw-semibold">{{ $line->criteria }}</td>
                  <td>{{ $line->target ?? '—' }}</td>
                  <td>{{ $line->scored ?? '—' }}</td>
                  <td>{{ $line->merit !== null ? number_format($line->merit, 1) : '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endif
@endsection
