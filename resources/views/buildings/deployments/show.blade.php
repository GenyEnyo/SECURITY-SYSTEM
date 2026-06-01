@extends('layouts.app')

@section('title', 'Deployment #' . $deployment->id . ' · M Dashboard')
@section('page', 'locations')
@section('crumbs', json_encode([
    ['label' => 'Setups'],
    ['label' => 'Locations', 'href' => '/locations'],
    ['label' => $building->name, 'href' => '/buildings/' . $building->id . '/deployments'],
    ['label' => '#' . $deployment->id],
]))

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">Deployment #{{ $deployment->id }}</h1>
      <p class="page-subtitle">{{ $deployment->shift->name }} shift · {{ $deployment->start_at->format('d M Y, H:i') }}</p>
    </div>
    <div class="actions d-flex gap-2">
      <a class="btn btn-primary" href="{{ route('buildings.deployments.index', $building) }}">
        <i class="bi bi-arrow-left me-2"></i>Back
      </a>
      <a class="btn btn-warning" href="{{ route('buildings.deployments.edit', [$building, $deployment]) }}">
        <i class="bi bi-pencil-square me-2"></i>Edit
      </a>
      <form action="{{ route('buildings.deployments.destroy', [$building, $deployment]) }}" method="POST"
            onsubmit="return confirm('Delete this deployment?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Delete</button>
      </form>
    </div>
  </div>

  <div class="shadow-card p-4">
    <dl class="row g-3 mb-0" style="font-size:15px;">
      <dt class="col-sm-3 muted fw-7">Building</dt>
      <dd class="col-sm-9">{{ $building->name }}</dd>

      <dt class="col-sm-3 muted fw-7">Location</dt>
      <dd class="col-sm-9">{{ $building->location->name }}</dd>

      <dt class="col-sm-3 muted fw-7">Security company</dt>
      <dd class="col-sm-9">{{ $deployment->securityCompany->name }}</dd>

      <dt class="col-sm-3 muted fw-7">Shift</dt>
      <dd class="col-sm-9">{{ $deployment->shift->name }}</dd>

      <dt class="col-sm-3 muted fw-7">Supervising officer</dt>
      <dd class="col-sm-9">{{ $deployment->supervising_officer }}</dd>

      <dt class="col-sm-3 muted fw-7">Number of guards</dt>
      <dd class="col-sm-9">{{ $deployment->number_of_guards }}</dd>

      <dt class="col-sm-3 muted fw-7">Start</dt>
      <dd class="col-sm-9">{{ $deployment->start_at->format('d M Y, H:i') }}</dd>

      <dt class="col-sm-3 muted fw-7">End</dt>
      <dd class="col-sm-9">{{ $deployment->end_at->format('d M Y, H:i') }}</dd>

      <dt class="col-sm-3 muted fw-7">Notes</dt>
      <dd class="col-sm-9">{{ $deployment->notes ?: '—' }}</dd>
    </dl>
  </div>
@endsection
