@extends('layouts.app')

@section('title', 'Edit scorecard · ' . $record->date->format('d M Y') . ' · M Dashboard')
@section('page', 'kpi-records')
@section('crumbs', json_encode([
    ['label' => 'Records'],
    ['label' => 'KPI Records', 'href' => '/kpi/records'],
    ['label' => $record->date->format('d M Y'), 'href' => '/kpi/records/' . $record->id],
    ['label' => 'Edit'],
]))

@section('content')
  @php
    $standardLines = $record->lines->whereNull('place_id');
    $beatLines     = $record->lines->whereNotNull('place_id');
  @endphp

  <div class="page-head">
    <div>
      <h1 class="page-title">Edit scorecard</h1>
      <p class="page-subtitle">{{ $record->location_name }} · {{ $record->building_name }} · {{ $record->date->format('d M Y') }}</p>
    </div>
    <div class="actions">
      <a class="btn btn-outline-primary" href="{{ route('records.show', $record) }}">
        <i class="bi bi-arrow-left me-2"></i>Back
      </a>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger" role="alert" style="background:rgba(252,51,32,.10);border:1px solid var(--brand-danger);border-radius:10px;padding:12px 18px;">
      @foreach ($errors->all() as $message)
        <div><i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('records.update', $record) }}">
    @csrf
    @method('PUT')

    <div class="shadow-card p-4 mb-3">
      <dl class="row g-3 mb-0" style="font-size:15px;">
        <dt class="col-sm-3 muted fw-7">Location</dt>
        <dd class="col-sm-9">{{ $record->location_name }}</dd>

        <dt class="col-sm-3 muted fw-7">Building</dt>
        <dd class="col-sm-9">{{ $record->building_name }}</dd>

        <dt class="col-sm-3 muted fw-7">Date</dt>
        <dd class="col-sm-9">{{ $record->date->format('d M Y') }}</dd>
      </dl>
      <p class="muted mb-0 mt-2" style="font-size:12px;">Location, building and date are locked. Edit the scores and comments — merit is recalculated automatically on save.</p>
    </div>

    <table class="brand-table">
      <thead>
        <tr>
          <th>KPI group</th>
          <th>Criteria</th>
          <th style="width:110px">Target</th>
          <th style="width:130px">Scored</th>
          <th style="width:130px">Merit <span class="muted fw-5" style="font-size:11px;">(auto)</span></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($standardLines as $line)
          <tr>
            <td>{{ $line->group?->name ?? '—' }}</td>
            <td class="fw-7">{{ $line->criteria }}</td>
            <td>{{ $line->target ?? '—' }}</td>
            <td><input type="number" min="0" name="lines[{{ $line->id }}][scored]" value="{{ old('lines.' . $line->id . '.scored', $line->scored) }}" class="brand-input"></td>
            <td class="muted">{{ $line->merit !== null ? number_format($line->merit, 1) : '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    @if ($beatLines->isNotEmpty())
      <div class="section-divider">Deployment beats</div>
      <table class="brand-table">
        <thead>
          <tr>
            <th>Place</th>
            <th style="width:140px">Est. guards</th>
            <th style="width:130px">Scored</th>
            <th style="width:130px">Merit <span class="muted fw-5" style="font-size:11px;">(auto)</span></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($beatLines as $line)
            <tr>
              <td class="fw-7">{{ $line->criteria }}</td>
              <td>{{ $line->target ?? '—' }}</td>
              <td><input type="number" min="0" name="lines[{{ $line->id }}][scored]" value="{{ old('lines.' . $line->id . '.scored', $line->scored) }}" class="brand-input"></td>
              <td class="muted">{{ $line->merit !== null ? number_format($line->merit, 1) : '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    <div class="shadow-card p-4 mt-3">
      <label class="field-label" for="comments">Comments</label>
      <textarea name="comments" id="comments" rows="3" class="brand-input" style="width:100%;">{{ old('comments', $record->comments) }}</textarea>
    </div>

    <div class="d-flex gap-2 mt-3">
      <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-2"></i>Save changes</button>
      <a class="btn btn-outline-primary" href="{{ route('records.show', $record) }}">Cancel</a>
    </div>
  </form>
@endsection
