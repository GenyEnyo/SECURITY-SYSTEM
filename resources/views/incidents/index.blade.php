@extends('layouts.app')

@section('title', 'Incidents · M Dashboard')
@section('page', 'incidents')
@section('crumbs', '[{"label":"Incidents"}]')

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">Incidents</h1>
      <p class="page-subtitle">Manage incident reporting here</p>
    </div>
    <div class="actions">
      <a class="btn btn-primary" href="{{ route('incidents.create') }}">
        <i class="bi bi-plus-square me-2"></i>Add
      </a>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success" role="alert" style="background:rgba(61,179,110,.12);border:1px solid var(--brand-success);border-radius:10px;padding:12px 18px;">
      <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
    </div>
  @endif

  <div class="toolbar">
    <div class="spacer"></div>
    <div class="search-shadow">
      <i class="bi bi-search"></i>
      <input type="search" placeholder="search for incident...">
    </div>
  </div>

  <table class="brand-table">
    <thead>
      <tr>
        <th style="width:80px">No.</th>
        <th>Date</th>
        <th>Incident Type</th>
        <th>Location</th>
        <th>Status</th>
        <th>Severity</th>
        <th class="actions-col">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($occurrences as $occurrence)
        <tr>
          <td class="fw-7">{{ $occurrence->id }}</td>
          <td>{{ $occurrence->occurred_at->format('d/m/y') }}</td>
          <td>{{ $occurrence->incidentType->name }}</td>
          <td>{{ $occurrence->location->name }}</td>
          <td><span class="pill pill-{{ strtolower($occurrence->status->name) }}">{{ $occurrence->status->name }}</span></td>
          <td><span class="pill" style="background:{{ $occurrence->severity->color }}1f;color:{{ $occurrence->severity->color }};">{{ $occurrence->severity->name }}</span></td>
          <td>
            <div class="row-actions">
              <a href="{{ route('incidents.show', $occurrence) }}" class="ra-btn" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></a>
              @if (! $occurrence->isLocked())
                <a href="{{ route('incidents.edit', $occurrence) }}" class="ra-btn" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil-square"></i></a>
                <form action="{{ route('incidents.destroy', $occurrence) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this incident? This cannot be undone.');">
                  @csrf @method('DELETE')
                  <button type="submit" class="ra-btn danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button>
                </form>
              @else
                <span class="ra-btn" style="opacity:.45;cursor:default;" data-bs-toggle="tooltip"
                      title="Locked — acknowledged or older than 1 day"><i class="bi bi-lock"></i></span>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center muted py-4">
            No incidents yet — <a href="{{ route('incidents.create') }}">add one</a>.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-3">
    {{ $occurrences->links() }}
  </div>
@endsection
