@extends('layouts.app')

@section('title', 'KPI Records · M Dashboard')
@section('page-title', 'KPI Records')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Records</a></li>
  <li class="breadcrumb-item active">KPI Records</li>
@endsection

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="mb-0">KPI Records</h4>
      <p class="text-muted mb-0">Saved scorecards per location and building, per day</p>
    </div>
    <a href="{{ route('kpi.reports.monthly', array_filter($filters)) }}" class="btn btn-outline-primary">
      <i class="ti ti-printer me-1"></i>Print month
    </a>
  </div>

  @if (session('status'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
      <i class="ti ti-circle-check me-2 fs-lg"></i>{{ session('status') }}
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
        <div>
          <label class="form-label">Month</label>
          <input type="month" name="month" value="{{ $filters['month'] ?? '' }}" class="form-control" style="width:160px;">
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
          @if (array_filter($filters))
            <a href="{{ route('records.index') }}" class="btn btn-outline-primary">Clear</a>
          @endif
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Location</th>
              <th>Building</th>
              <th style="width:90px">Lines</th>
              <th style="width:110px">Score <span class="text-muted" style="font-size:11px;">/100</span></th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($records as $record)
              <tr>
                <td class="fw-semibold">{{ $record->date->format('d M Y') }}</td>
                <td>{{ $record->location_name }}</td>
                <td>{{ $record->building_name }}</td>
                <td>{{ $record->lines_count }}</td>
                <td>{{ number_format($record->lines_sum_merit ?? 0, 1) }}</td>
                <td class="text-end">
                  <a href="{{ route('records.show', $record) }}" class="btn btn-sm btn-icon btn-soft-secondary" title="View"><i class="ti ti-eye"></i></a>
                  <a href="{{ route('records.edit', $record) }}" class="btn btn-sm btn-icon btn-soft-secondary" title="Edit"><i class="ti ti-pencil"></i></a>
                  <form action="{{ route('records.destroy', $record) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this scorecard? This cannot be undone.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-icon btn-soft-danger" title="Delete"><i class="ti ti-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  No scorecards saved yet — <a href="/kpi/entries">add one</a>.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">
    {{ $records->links() }}
  </div>
@endsection
