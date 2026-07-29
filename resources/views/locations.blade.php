@extends('layouts.app')

@section('title', 'Locations · M Dashboard')
@section('page-title', 'Locations & Company Management')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Setups</a></li>
  <li class="breadcrumb-item active">Locations</li>
@endsection

@section('content')

  @if (session('status'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
      <i class="ti ti-circle-check me-2 fs-lg"></i>{{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger" role="alert">
      @foreach ($errors->all() as $message)
        <div><i class="ti ti-alert-triangle me-2"></i>{{ $message }}</div>
      @endforeach
    </div>
  @endif

  <!-- Buildings -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="mb-0">Buildings</h4>
      <p class="text-muted mb-0">Posts &amp; sites you manage</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBuilding">
      <i class="ti ti-plus me-1"></i>Add building
    </button>
  </div>

  <div class="row">
    @forelse ($buildings as $building)
      <div class="col-xl-3 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <span class="avatar-md d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded fs-22">
                <i class="ti ti-building"></i>
              </span>
              <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-icon btn-soft-secondary js-edit-building"
                        title="Edit"
                        data-id="{{ $building->id }}"
                        data-name="{{ $building->name }}"
                        data-location-id="{{ $building->location_id }}">
                  <i class="ti ti-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-soft-danger js-delete-building"
                        title="Delete"
                        data-id="{{ $building->id }}"
                        data-name="{{ $building->name }}">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            </div>
            <h4 class="mb-1 fw-bold">{{ $building->name }}</h4>
            <p class="text-muted small mb-3"><i class="ti ti-map-pin me-1"></i>{{ $building->location->name ?? '—' }}</p>
            <a href="{{ route('buildings.places.index', $building) }}" class="btn btn-sm btn-primary w-100">
              <i class="ti ti-settings me-1"></i>Manage places
            </a>
          </div>
        </div>
      </div>
    @empty
    @endforelse

    <!-- Add new tile -->
    <div class="col-xl-3 col-md-6">
      <button type="button" class="card w-100 h-100 border-2 border-dashed bg-transparent js-add-building-tile"
              data-bs-toggle="modal" data-bs-target="#addBuilding" style="border-style:dashed!important;">
        <div class="card-body d-flex flex-column align-items-center justify-content-center text-primary py-4">
          <i class="ti ti-circle-plus fs-36 mb-2"></i>
          <div class="fw-bold">Add new building</div>
          <div class="text-muted small">Posts &amp; sites</div>
        </div>
      </button>
    </div>
  </div>

  <!-- Locations -->
  <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
    <div>
      <h4 class="mb-0">Locations</h4>
      <p class="text-muted mb-0">Add the locations buildings can belong to</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocation">
      <i class="ti ti-plus me-1"></i>Add location
    </button>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:80px">No.</th>
              <th>Name</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($locations as $location)
              <tr>
                <td class="fw-semibold">{{ $loop->iteration }}</td>
                <td>{{ $location->name }}</td>
                <td class="text-end">
                  <button type="button" class="btn btn-sm btn-icon btn-soft-secondary js-edit-location"
                          title="Edit"
                          data-id="{{ $location->id }}"
                          data-name="{{ $location->name }}">
                    <i class="ti ti-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-icon btn-soft-danger js-delete-location"
                          title="Delete"
                          data-id="{{ $location->id }}"
                          data-name="{{ $location->name }}">
                    <i class="ti ti-trash"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-muted">No locations yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @include('partials.buildings.add-building')
  @include('partials.buildings.edit-building')
  @include('partials.buildings.delete-building')

  @include('partials.locations.add-location')
  @include('partials.locations.edit-location')
  @include('partials.locations.delete-location')
@endsection

@push('scripts')
  <script>
    document.addEventListener('click', (e) => {
      const t = e.target.closest('.js-edit-building, .js-delete-building, .js-edit-location, .js-delete-location');
      if (!t) return;
      const d = t.dataset;
      const open = (id, fill) => {
        const el = document.getElementById(id);
        fill(el);
        new bootstrap.Modal(el).show();
      };
      if (t.classList.contains('js-edit-building')) {
        open('editBuilding', (el) => {
          el.querySelector('form').action = `/buildings/${d.id}`;
          el.querySelector('[name="name"]').value = d.name;
          el.querySelector('[name="location_id"]').value = d.locationId;
        });
      } else if (t.classList.contains('js-delete-building')) {
        open('deleteBuilding', (el) => {
          el.querySelector('form').action = `/buildings/${d.id}`;
          el.querySelector('.target-name').textContent = d.name;
        });
      } else if (t.classList.contains('js-edit-location')) {
        open('editLocation', (el) => {
          el.querySelector('form').action = `/locations/${d.id}`;
          el.querySelector('[name="name"]').value = d.name;
        });
      } else {
        open('deleteLocation', (el) => {
          el.querySelector('form').action = `/locations/${d.id}`;
          el.querySelector('.target-name').textContent = d.name;
        });
      }
    });
  </script>
@endpush
