@extends('layouts.app')

@section('title', 'Deployment #' . $deployment->id . ' · M Dashboard')
@section('page-title', 'Deployment #' . $deployment->id)
@section('crumbs')
  <li class="breadcrumb-item">Setups</li>
  <li class="breadcrumb-item"><a href="{{ url('/locations') }}">Locations</a></li>
  <li class="breadcrumb-item"><a href="{{ url('/buildings/' . $building->id . '/deployments') }}">{{ $building->name }}</a></li>
  <li class="breadcrumb-item active">#{{ $deployment->id }}</li>
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Deployment #{{ $deployment->id }}</h4>
      <p class="text-muted mb-0">{{ $deployment->shift->name }} shift · {{ $deployment->start_at->format('d M Y, H:i') }}</p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-soft-primary" href="{{ route('buildings.deployments.index', $building) }}">
        <i class="ti ti-arrow-left me-1"></i>Back
      </a>
      <a class="btn btn-warning" href="{{ route('buildings.deployments.edit', [$building, $deployment]) }}">
        <i class="ti ti-pencil me-1"></i>Edit
      </a>
      <form action="{{ route('buildings.deployments.destroy', [$building, $deployment]) }}" method="POST"
            onsubmit="return confirm('Delete this deployment?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="ti ti-trash me-1"></i>Delete</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row g-3 mb-0">
        <dt class="col-sm-3 text-muted fw-semibold">Building</dt>
        <dd class="col-sm-9">{{ $building->name }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Location</dt>
        <dd class="col-sm-9">{{ $building->location->name }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Security company</dt>
        <dd class="col-sm-9">{{ $deployment->securityCompany->name }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Shift</dt>
        <dd class="col-sm-9">{{ $deployment->shift->name }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Supervising officer</dt>
        <dd class="col-sm-9">{{ $deployment->supervising_officer }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Number of guards</dt>
        <dd class="col-sm-9">{{ $deployment->number_of_guards }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Start</dt>
        <dd class="col-sm-9">{{ $deployment->start_at->format('d M Y, H:i') }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">End</dt>
        <dd class="col-sm-9">{{ $deployment->end_at->format('d M Y, H:i') }}</dd>

        <dt class="col-sm-3 text-muted fw-semibold">Notes</dt>
        <dd class="col-sm-9">{{ $deployment->notes ?: '—' }}</dd>
      </dl>
    </div>
  </div>
@endsection
