@extends('layouts.app')

@section('title', 'Incident #' . $incident->id . ' · M Dashboard')
@section('page-title', 'Incident #' . $incident->id)
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/incidents') }}">Incidents</a></li>
  <li class="breadcrumb-item active">#{{ $incident->id }}</li>
@endsection

@section('content')
  @php
    $statusColors = [
      'low' => 'success', 'resolved' => 'success', 'closed' => 'success',
      'reviewing' => 'info',
      'medium' => 'warning', 'reported' => 'warning',
      'urgent' => 'danger', 'escalated' => 'danger',
    ];
    $sc = $statusColors[strtolower($incident->status->name)] ?? 'secondary';
  @endphp

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Incident #{{ $incident->id }}</h4>
      <p class="text-muted mb-0">{{ $incident->incidentType->name }} · {{ $incident->occurred_at->format('d M Y, H:i') }}</p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-soft-primary" href="{{ route('incidents.index') }}"><i class="ti ti-arrow-left me-1"></i>Back</a>
      @unless ($incident->isAcknowledged())
        <form action="{{ route('incidents.acknowledge', $incident) }}" method="POST"
              onsubmit="return confirm('Acknowledge receipt? The reporter will no longer be able to edit or delete it.');">
          @csrf
          <button type="submit" class="btn btn-success"><i class="ti ti-circle-check me-1"></i>Acknowledge</button>
        </form>
      @endunless
      @if (! $incident->isLocked())
        <a class="btn btn-warning" href="{{ route('incidents.edit', $incident) }}"><i class="ti ti-pencil me-1"></i>Edit</a>
        <form action="{{ route('incidents.destroy', $incident) }}" method="POST" onsubmit="return confirm('Delete this incident?');">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger"><i class="ti ti-trash me-1"></i>Delete</button>
        </form>
      @endif
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row g-3 mb-0">
        <dt class="col-sm-3 text-muted fw-semibold">Incident type</dt>
        <dd class="col-sm-9">{{ $incident->incidentType->name }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Date &amp; time</dt>
        <dd class="col-sm-9">{{ $incident->occurred_at->format('d M Y, H:i') }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Location</dt>
        <dd class="col-sm-9">{{ $incident->location->name }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Building</dt>
        <dd class="col-sm-9">{{ $incident->building?->name ?: '—' }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Specific location</dt>
        <dd class="col-sm-9">{{ $incident->place?->name ?: '—' }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Severity</dt>
        <dd class="col-sm-9"><span class="badge" style="background:{{ $incident->severity->color }}1f;color:{{ $incident->severity->color }};">{{ $incident->severity->name }}</span></dd>

        <dt class="col-sm-3 text-muted fw-semibold">Status</dt>
        <dd class="col-sm-9"><span class="badge bg-{{ $sc }}-subtle text-{{ $sc }}">{{ $incident->status->name }}</span></dd>

        <dt class="col-sm-3 text-muted fw-semibold">Acknowledged</dt>
        <dd class="col-sm-9">{{ $incident->acknowledged_at?->format('d M Y, H:i') ?: '—' }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Reported by</dt>
        <dd class="col-sm-9">{{ $incident->user->name }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Description</dt>
        <dd class="col-sm-9">{{ $incident->description ?: '—' }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Attachment</dt>
        <dd class="col-sm-9">
          @if ($incident->attachment_path)
            <a href="{{ Storage::url($incident->attachment_path) }}" target="_blank" rel="noopener">
              <i class="ti ti-paperclip me-1"></i>{{ basename($incident->attachment_path) }}
            </a>
          @else
            —
          @endif
        </dd>
      </dl>
    </div>
  </div>
@endsection
