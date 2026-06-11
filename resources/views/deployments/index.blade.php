@extends('layouts.app')

@section('title', 'Deployments · M Dashboard')
@section('page', 'deployments')
@section('crumbs', '[{"label":"Setups"},{"label":"Deployments"}]')

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">Deployments</h1>
      <p class="page-subtitle">Pick a building to manage its deployments</p>
    </div>
  </div>

  <div class="row g-3">
    @forelse ($buildings as $building)
      <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="location-tile">
          <div>
            <div class="title">{{ $building->name }}</div>
            <div class="sub muted mt-2">{{ $building->location->name }}</div>
          </div>
          <a href="{{ route('buildings.deployments.index', $building) }}" class="btn btn-primary">Manage</a>
        </div>
      </div>
    @empty
      <div class="col-12">
        <p class="muted fw-5">No buildings yet. Add one from the Locations page first.</p>
      </div>
    @endforelse
  </div>
@endsection
