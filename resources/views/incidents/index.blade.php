@extends('layouts.app')

@section('title', 'Incidents · M Dashboard')
@section('page-title', 'Incidents')
@section('crumbs')
  <li class="breadcrumb-item active">Incidents</li>
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Incidents</h4>
      <p class="text-muted mb-0">Manage incident reporting here</p>
    </div>
    <a class="btn btn-primary" href="{{ route('incidents.create') }}">
      <i class="ti ti-plus me-1"></i>Add
    </a>
  </div>

  @if (session('status'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
      <i class="ti ti-circle-check me-2 fs-lg"></i>{{ session('status') }}
    </div>
  @endif

  @php
    $statusColors = [
      'low' => 'success', 'resolved' => 'success', 'closed' => 'success',
      'reviewing' => 'info',
      'medium' => 'warning', 'reported' => 'warning',
      'urgent' => 'danger', 'escalated' => 'danger',
    ];
  @endphp

  <div class="card">
    <div class="card-header">
      <h4 class="card-title mb-0">Incidents</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table data-tables="basic" class="table table-striped dt-responsive align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:80px">No.</th>
              <th>Date</th>
              <th>Incident Type</th>
              <th>Location</th>
              <th>Status</th>
              <th>Severity</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($occurrences as $occurrence)
              @php $sc = $statusColors[strtolower($occurrence->status->name)] ?? 'secondary'; @endphp
              <tr>
                <td class="fw-semibold">{{ $occurrence->id }}</td>
                <td>{{ $occurrence->occurred_at->format('d/m/y') }}</td>
                <td>{{ $occurrence->incidentType->name }}</td>
                <td>{{ $occurrence->location->name }}</td>
                <td><span class="badge bg-{{ $sc }}-subtle text-{{ $sc }}">{{ $occurrence->status->name }}</span></td>
                <td><span class="badge" style="background:{{ $occurrence->severity->color }}1f;color:{{ $occurrence->severity->color }};">{{ $occurrence->severity->name }}</span></td>
                <td class="text-end">
                  <a href="{{ route('incidents.show', $occurrence) }}" class="btn btn-sm btn-icon btn-soft-secondary" title="View"><i class="ti ti-eye"></i></a>
                  @if (! $occurrence->isLocked())
                    <a href="{{ route('incidents.edit', $occurrence) }}" class="btn btn-sm btn-icon btn-soft-secondary" title="Edit"><i class="ti ti-pencil"></i></a>
                    <form action="{{ route('incidents.destroy', $occurrence) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this incident? This cannot be undone.');">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-icon btn-soft-danger" title="Delete"><i class="ti ti-trash"></i></button>
                    </form>
                  @else
                    <span class="btn btn-sm btn-icon btn-soft-secondary" style="opacity:.45;cursor:default;"
                          title="Locked — acknowledged or older than 1 day"><i class="ti ti-lock"></i></span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  No incidents yet — <a href="{{ route('incidents.create') }}">add one</a>.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

@push('head')
  <link href="{{ asset('theme/plugins/datatables/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
  <script src="{{ asset('theme/plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('theme/plugins/datatables/dataTables.min.js') }}"></script>
  <script src="{{ asset('theme/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('theme/plugins/datatables/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('theme/plugins/datatables/responsive.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('theme/js/pages/datatables-basic.js') }}"></script>
@endpush
