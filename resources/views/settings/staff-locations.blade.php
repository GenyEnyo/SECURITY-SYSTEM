@extends('layouts.app')

@section('title', 'Settings — Staff Locations · M Dashboard')
@section('page-title', 'Settings — Staff Locations')
@section('crumbs')
  <li class="breadcrumb-item">Settings</li>
  <li class="breadcrumb-item active">Staff Locations</li>
@endsection

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Settings — Staff Locations</h4>
    <p class="text-muted mb-0">
      Correct a staff member's location when HR's is wrong. The correction is used until HR changes that person's location, then HR's is used again.
    </p>
  </div>

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

  <div class="card">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
          <label class="form-label">Search</label>
          <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Name or staff number">
        </div>
        <div class="col-md-3">
          <label class="form-label">Location</label>
          <select name="location_id" class="form-select">
            <option value="">All locations</option>
            @foreach ($locations as $location)
              <option value="{{ $location->id }}" @selected(($filters['location_id'] ?? '') == $location->id)>{{ $location->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Show</label>
          <select name="filter" class="form-select">
            <option value="">All staff</option>
            <option value="unrecognised" @selected(($filters['filter'] ?? '') === 'unrecognised')>Location not recognised</option>
            <option value="corrected" @selected(($filters['filter'] ?? '') === 'corrected')>Corrected by admin</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="ti ti-filter me-1"></i>Filter</button>
          <a href="{{ route('settings.staff-locations.index') }}" class="btn btn-light">Reset</a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th>Staff no.</th>
              <th>Name</th>
              <th>Job</th>
              <th>HR location</th>
              <th style="min-width:280px">App location</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($staff as $person)
              @php $hrKnown = in_array(strtolower(trim((string) $person->location_name)), $knownNames, true); @endphp
              <tr>
                <td class="fw-semibold">{{ $person->staff_number }}</td>
                <td>{{ $person->full_name ?? '—' }}</td>
                <td class="text-muted">{{ $person->job_name ?? '—' }}</td>
                <td>
                  {{ $person->location_name ?? '—' }}
                  @if (! $hrKnown && ! $person->hasLocationOverride())
                    <div class="text-danger fs-12">Not a location in this app</div>
                  @endif
                </td>
                <td>
                  <form method="POST" action="{{ route('settings.staff-locations.update', $person) }}" class="d-flex gap-2 align-items-center">
                    @csrf
                    @method('PUT')
                    <select name="location_id" class="form-select form-select-sm" style="max-width:180px">
                      <option value="">Use HR location</option>
                      @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected($person->location_id === $location->id)>{{ $location->name }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-success" title="Save" aria-label="Save"><i class="ti ti-check"></i></button>
                    @if ($person->hasLocationOverride())
                      <span class="badge bg-warning-subtle text-warning" title="HR says: {{ $person->location_name ?? '—' }}">Corrected</span>
                    @endif
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted py-4">No staff found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted fs-13">{{ $staff->total() }} staff</div>
        {{ $staff->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
@endsection
