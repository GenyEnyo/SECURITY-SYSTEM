@extends('layouts.app')

@section('title', 'Edit scorecard · ' . $record->date->format('d M Y') . ' · M Dashboard')
@section('page-title', 'Edit Scorecard')
@section('crumbs')
  <li class="breadcrumb-item">Records</li>
  <li class="breadcrumb-item"><a href="/kpi/records">KPI Records</a></li>
  <li class="breadcrumb-item"><a href="/kpi/records/{{ $record->id }}">{{ $record->date->format('d M Y') }}</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @php
    $standardLines = $record->lines->whereNull('place_id');
    $beatLines     = $record->lines->whereNotNull('place_id');
  @endphp

  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="mb-0">Edit scorecard</h4>
      <p class="text-muted mb-0">{{ $record->location_name }} · {{ $record->building_name }} · {{ $record->date->format('d M Y') }}</p>
    </div>
    <a class="btn btn-soft-primary" href="{{ route('records.show', $record) }}">
      <i class="ti ti-arrow-left me-1"></i>Back
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger" role="alert">
      @foreach ($errors->all() as $message)
        <div><i class="ti ti-alert-triangle me-2"></i>{{ $message }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('records.update', $record) }}">
    @csrf
    @method('PUT')

    <div class="card">
      <div class="card-body">
        <dl class="row g-3 mb-0">
          <dt class="col-sm-3 text-muted fw-semibold">Location</dt>
          <dd class="col-sm-9">{{ $record->location_name }}</dd>

          <dt class="col-sm-3 text-muted fw-semibold">Building</dt>
          <dd class="col-sm-9">{{ $record->building_name }}</dd>

          <dt class="col-sm-3 text-muted fw-semibold">Date</dt>
          <dd class="col-sm-9">{{ $record->date->format('d M Y') }}</dd>
        </dl>
        <p class="text-muted mb-0 mt-2 small">Location, building and date are locked. Edit the scores and comments — merit is recalculated automatically on save.</p>
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
                <th style="width:130px">Scored</th>
                <th style="width:130px">Merit <span class="text-muted" style="font-size:11px;">(auto)</span></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($standardLines as $line)
                <tr>
                  <td>{{ $line->group?->name ?? '—' }}</td>
                  <td class="fw-semibold">{{ $line->criteria }}</td>
                  <td>{{ $line->target ?? '—' }}</td>
                  <td><input type="number" min="0" name="lines[{{ $line->id }}][scored]" value="{{ old('lines.' . $line->id . '.scored', $line->scored) }}" class="form-control"></td>
                  <td class="text-muted">{{ $line->merit !== null ? number_format($line->merit, 1) : '—' }}</td>
                </tr>
              @endforeach
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
                  <th style="width:130px">Scored</th>
                  <th style="width:130px">Merit <span class="text-muted" style="font-size:11px;">(auto)</span></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($beatLines as $line)
                  <tr>
                    <td class="fw-semibold">{{ $line->criteria }}</td>
                    <td>{{ $line->target ?? '—' }}</td>
                    <td><input type="number" min="0" name="lines[{{ $line->id }}][scored]" value="{{ old('lines.' . $line->id . '.scored', $line->scored) }}" class="form-control"></td>
                    <td class="text-muted">{{ $line->merit !== null ? number_format($line->merit, 1) : '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif

    <div class="card">
      <div class="card-body">
        <label class="form-label" for="comments">Comments</label>
        <textarea name="comments" id="comments" rows="3" class="form-control">{{ old('comments', $record->comments) }}</textarea>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Save changes</button>
      <a class="btn btn-light" href="{{ route('records.show', $record) }}">Cancel</a>
    </div>
  </form>
@endsection
