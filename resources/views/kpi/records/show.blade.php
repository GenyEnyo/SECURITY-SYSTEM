@extends('layouts.app')

@section('title', 'Scorecard · ' . $record->date->format('d M Y') . ' · M Dashboard')
@section('page', 'kpi-records')
@section('crumbs', json_encode([
    ['label' => 'Records'],
    ['label' => 'KPI Records', 'href' => '/kpi/records'],
    ['label' => $record->date->format('d M Y')],
]))

@section('content')
  @php
    $standardLines = $record->lines->whereNull('place_id');
    $beatLines     = $record->lines->whereNotNull('place_id');
  @endphp

  <div class="page-head">
    <div>
      <h1 class="page-title">{{ $record->location_name }} · {{ $record->building_name }}</h1>
      <p class="page-subtitle">Scorecard for {{ $record->date->format('d M Y') }}</p>
    </div>
    <div class="actions d-flex gap-2">
      <a class="btn btn-primary" href="{{ route('records.index') }}">
        <i class="bi bi-arrow-left me-2"></i>Back
      </a>
      <a class="btn btn-warning" href="{{ route('records.edit', $record) }}">
        <i class="bi bi-pencil-square me-2"></i>Edit
      </a>
      <form action="{{ route('records.destroy', $record) }}" method="POST"
            onsubmit="return confirm('Delete this scorecard? This cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Delete</button>
      </form>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success" role="alert" style="background:rgba(61,179,110,.12);border:1px solid var(--brand-success);border-radius:10px;padding:12px 18px;">
      <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
    </div>
  @endif

  <div class="shadow-card p-4 mb-3">
    <dl class="row g-3 mb-0" style="font-size:15px;">
      <dt class="col-sm-3 muted fw-7">Location</dt>
      <dd class="col-sm-9">{{ $record->location_name }}</dd>

      <dt class="col-sm-3 muted fw-7">Building</dt>
      <dd class="col-sm-9">{{ $record->building_name }}</dd>

      <dt class="col-sm-3 muted fw-7">Date</dt>
      <dd class="col-sm-9">{{ $record->date->format('d M Y') }}</dd>

      <dt class="col-sm-3 muted fw-7">Comments</dt>
      <dd class="col-sm-9">{{ $record->comments ?: '—' }}</dd>
    </dl>
  </div>

  <table class="brand-table">
    <thead>
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
          <td class="fw-7">{{ $line->criteria }}</td>
          <td>{{ $line->target ?? '—' }}</td>
          <td>{{ $line->scored ?? '—' }}</td>
          <td>{{ $line->merit !== null ? number_format($line->merit, 1) : '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center muted py-4">No KPI lines recorded.</td></tr>
      @endforelse
    </tbody>
  </table>

  @if ($beatLines->isNotEmpty())
    <div class="section-divider">Deployment beats</div>
    <table class="brand-table">
      <thead>
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
            <td class="fw-7">{{ $line->criteria }}</td>
            <td>{{ $line->target ?? '—' }}</td>
            <td>{{ $line->scored ?? '—' }}</td>
            <td>{{ $line->merit !== null ? number_format($line->merit, 1) : '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection
