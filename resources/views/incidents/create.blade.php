@extends('layouts.app')

@section('title', 'Report Incident · M Dashboard')
@section('page-title', 'Report Incident')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/incidents') }}">Incidents</a></li>
  <li class="breadcrumb-item active">Report</li>
@endsection

@section('content')
  <div class="row justify-content-center">
    <div class="col-xxl-10">

      <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Incident details</h4>
            <a class="btn btn-sm btn-soft-primary" href="{{ url('/incidents') }}">
              <i class="ti ti-arrow-left me-1"></i>Back
            </a>
          </div>

          <div class="card-body">
            <p class="text-muted">Report incidents on this form.</p>

            <div class="row g-3">

              <div class="col-lg-6">
                <label class="form-label" for="f-type">Incident Type</label>
                <select id="f-type" name="incident_type_id" class="form-select @error('incident_type_id') is-invalid @enderror" required>
                  <option value="" disabled @selected(! old('incident_type_id'))>Select incident type...</option>
                  @foreach ($incidentTypes as $type)
                    <option value="{{ $type->id }}" @selected(old('incident_type_id') == $type->id)>{{ $type->name }}</option>
                  @endforeach
                </select>
                @error('incident_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-date">Date and Time</label>
                <input id="f-date" name="occurred_at" type="datetime-local" class="form-control @error('occurred_at') is-invalid @enderror" value="{{ old('occurred_at') }}" required>
                @error('occurred_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-loc">Location</label>
                <select id="f-loc" name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                  <option value="" disabled @selected(! old('location_id'))>Select your location...</option>
                  @foreach ($locations as $location)
                    <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
                  @endforeach
                </select>
                @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-building">Building</label>
                <select id="f-building" name="building_id" class="form-select @error('building_id') is-invalid @enderror" required disabled>
                  <option value="" disabled selected>Select a location first...</option>
                </select>
                @error('building_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-place">Specific Location</label>
                <select id="f-place" name="place_id" class="form-select @error('place_id') is-invalid @enderror" required disabled>
                  <option value="" disabled selected>Select a building first...</option>
                </select>
                @error('place_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-sev">Severity / Priority Level</label>
                <select id="f-sev" name="severity_id" class="form-select @error('severity_id') is-invalid @enderror" required>
                  <option value="" disabled @selected(! old('severity_id'))>Select priority level...</option>
                  @foreach ($severities as $severity)
                    <option value="{{ $severity->id }}" @selected(old('severity_id') == $severity->id)>{{ $severity->name }}</option>
                  @endforeach
                </select>
                @error('severity_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-officer">Reporting Officer</label>
                <input id="f-officer" class="form-control" value="{{ auth()->user()->name }}" readonly>
              </div>

              <div class="col-12">
                <label class="form-label" for="f-desc">Description</label>
                <textarea id="f-desc" name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="The issue I want to report is...">{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-12">
                <label class="form-label" for="f-attachment">
                  Upload attachment
                  <span class="text-muted fw-normal">(.png, .jpg, .pdf, .docx — not more than 5MB)</span>
                </label>
                <input id="f-attachment" type="file" name="attachment"
                       class="form-control @error('attachment') is-invalid @enderror"
                       accept=".png,.jpg,.jpeg,.pdf,.docx">
                @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

            </div>
          </div>

          <div class="card-footer d-flex gap-2">
            <button type="submit" class="btn btn-success">
              <i class="ti ti-check me-1"></i>Save
            </button>
            <a href="{{ url('/incidents') }}" class="btn btn-light">
              <i class="ti ti-x me-1"></i>Cancel
            </a>
          </div>
        </div>
      </form>

    </div>
  </div>
@endsection

@push('scripts')
  <script>
    const BUILDINGS = @json($buildings);
    const PLACES = @json($places);
    const SELECTED = {
      location: @json(old('location_id')),
      building: @json(old('building_id')),
      place: @json(old('place_id')),
    };

    const locSel = document.getElementById('f-loc');
    const buildingSel = document.getElementById('f-building');
    const placeSel = document.getElementById('f-place');

    function fill(select, items, placeholder, selectedId) {
      select.innerHTML = '';
      const ph = document.createElement('option');
      ph.value = '';
      ph.disabled = true;
      ph.textContent = placeholder;
      select.appendChild(ph);

      items.forEach((it) => {
        const opt = document.createElement('option');
        opt.value = it.id;
        opt.textContent = it.name;
        if (String(it.id) === String(selectedId)) opt.selected = true;
        select.appendChild(opt);
      });

      if (!items.some((it) => String(it.id) === String(selectedId))) ph.selected = true;
      select.disabled = items.length === 0;
    }

    function loadBuildings(selectedBuilding) {
      const locId = locSel.value;
      const items = BUILDINGS.filter((b) => String(b.location_id) === String(locId));
      fill(buildingSel, items, 'Select a building...', selectedBuilding);
    }

    function loadPlaces(selectedPlace) {
      const buildingId = buildingSel.value;
      const items = PLACES.filter((p) => String(p.building_id) === String(buildingId));
      fill(placeSel, items, 'Select a specific location...', selectedPlace);
    }

    locSel.addEventListener('change', () => {
      loadBuildings(null);
      loadPlaces(null);
    });
    buildingSel.addEventListener('change', () => loadPlaces(null));

    // Re-apply previously chosen values (e.g. after a validation error)
    if (SELECTED.location) {
      loadBuildings(SELECTED.building);
      loadPlaces(SELECTED.place);
    }
  </script>
@endpush
