@extends('layouts.app')

@section('title', 'KPI Records · M Dashboard')
@section('page', 'kpi-records')
@section('crumbs', '[{"label":"Records"},{"label":"KPI Records"}]')

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">KPI Records</h1>
      <p class="page-subtitle">Saved scorecards per location and building, per day</p>
    </div>
    <div class="actions">
      <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
        <input type="month" name="month" value="{{ $filters['month'] ?? '' }}" class="brand-input" style="width:160px;">
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
        @if (array_filter($filters))
          <a href="{{ route('records.index') }}" class="btn btn-outline-primary">Clear</a>
        @endif
      </form>
      <a href="{{ route('kpi.reports.monthly', array_filter($filters)) }}" class="btn btn-outline-primary">
        <i class="bi bi-printer me-1"></i>Print month
      </a>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success" role="alert" style="background:rgba(61,179,110,.12);border:1px solid var(--brand-success);border-radius:10px;padding:12px 18px;">
      <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
    </div>
  @endif

  <table class="brand-table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Location</th>
        <th>Building</th>
        <th style="width:90px">Lines</th>
        <th style="width:110px">Score <span class="muted fw-5" style="font-size:11px;">/100</span></th>
        <th class="actions-col">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($records as $record)
        <tr>
          <td class="fw-7">{{ $record->date->format('d M Y') }}</td>
          <td>{{ $record->location_name }}</td>
          <td>{{ $record->building_name }}</td>
          <td>{{ $record->lines_count }}</td>
          <td>{{ number_format($record->lines_sum_merit ?? 0, 1) }}</td>
          <td>
            <div class="row-actions">
              <a href="{{ route('records.show', $record) }}" class="ra-btn" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></a>
              <a href="{{ route('records.edit', $record) }}" class="ra-btn" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil-square"></i></a>
              <form action="{{ route('records.destroy', $record) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this scorecard? This cannot be undone.');">
                @csrf @method('DELETE')
                <button type="submit" class="ra-btn danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center muted py-4">
            No scorecards saved yet — <a href="/kpi/entries">add one</a>.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-3">
    {{ $records->links() }}
  </div>
@endsection
