@extends('layouts.app')

@section('title', 'Deployments · M Dashboard')
@section('page-title', 'Deployments')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Setups</a></li>
  <li class="breadcrumb-item active">Deployments</li>
@endsection

@section('content')
  <div class="row mb-2">
    <div class="col">
      <p class="text-muted mb-3">Pick a building to manage its deployments.</p>
    </div>
  </div>

  <div class="row">
    @forelse ($buildings as $building)
      <div class="col-xl-4 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="d-flex align-items-center gap-3">
                <span class="avatar-md d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded fs-22">
                  <i class="ti ti-building"></i>
                </span>
                <div>
                  <h4 class="mb-1 fw-bold">{{ $building->name }}</h4>
                  <span class="text-muted small"><i class="ti ti-map-pin me-1"></i>{{ $building->location->name ?? '—' }}</span>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end align-items-center">
              <a href="{{ route('buildings.deployments.index', $building) }}" class="btn btn-primary btn-sm">
                <i class="ti ti-clipboard-list me-1"></i>Manage deployments
              </a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card">
          <div class="card-body text-center text-muted py-5">
            <i class="ti ti-building-off fs-36 d-block mb-2"></i>
            No buildings yet. Add one from the <a href="{{ url('/locations') }}">Locations</a> page first.
          </div>
        </div>
      </div>
    @endforelse
  </div>
@endsection
