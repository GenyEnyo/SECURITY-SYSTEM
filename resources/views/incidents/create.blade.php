@extends('layouts.app')

@section('title', 'Add Incident · M Dashboard')
@section('page', 'add-incident')
@section('crumbs', '[{"label":"Incidents","href":"/incidents"},{"label":"Add"}]')

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">Add Incident</h1>
      <p class="page-subtitle">Report incidents on this form</p>
    </div>
    <div class="actions">
      <a class="btn btn-primary" href="/incidents"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
  </div>

  <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">

      <div class="col-lg-6">
        <label class="field-label" for="f-type">Incident Type</label>
        <select id="f-type" name="incident_type_id" class="brand-input brand-select" required>
          <option value="" disabled @selected(! old('incident_type_id'))>Select incident type...</option>
          @foreach ($incidentTypes as $type)
            <option value="{{ $type->id }}" @selected(old('incident_type_id') == $type->id)>{{ $type->name }}</option>
          @endforeach
        </select>
        @error('incident_type_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-date">Date and Time</label>
        <div class="position-relative">
          <input id="f-date" name="occurred_at" type="datetime-local" class="brand-input" value="{{ old('occurred_at') }}" required>
          <i class="bi bi-calendar-event position-absolute" style="right:22px;top:50%;transform:translateY(-50%);font-size:24px;pointer-events:none;"></i>
        </div>
        @error('occurred_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-loc">Location</label>
        <select id="f-loc" name="location_id" class="brand-input brand-select" required>
          <option value="" disabled @selected(! old('location_id'))>Select your location...</option>
          @foreach ($locations as $location)
            <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
          @endforeach
        </select>
        @error('location_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-sev">Severity / Priority Level</label>
        <select id="f-sev" name="severity_id" class="brand-input brand-select" required>
          <option value="" disabled @selected(! old('severity_id'))>Select priority level...</option>
          @foreach ($severities as $severity)
            <option value="{{ $severity->id }}" @selected(old('severity_id') == $severity->id)>{{ $severity->name }}</option>
          @endforeach
        </select>
        @error('severity_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-officer">Reporting Officer</label>
        <input id="f-officer" class="brand-input readonly" value="Kwasi Ansah" readonly>
      </div>

      <div class="col-12">
        <label class="field-label" for="f-desc">Description</label>
        <textarea id="f-desc" name="description" class="brand-input" rows="4" placeholder="The issue I want to report is...">{{ old('description') }}</textarea>
        @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-12">
        <label class="field-label">Upload attachment <span class="muted fw-5" style="font-size:14px;">(.png, .jpg, .pdf, .docx — not more than 5MB)</span></label>
        <div class="brand-dropzone">
          <i class="bi bi-cloud-arrow-up"></i>
          <div class="dz-label">
            <div class="fw-7" style="font-size:16px;">Drop a file here, or click to browse</div>
            <div class="muted fw-5" style="font-size:14px;">Max 5MB · PNG, JPG, PDF, DOCX</div>
          </div>
          <input type="file" name="attachment" accept=".png,.jpg,.jpeg,.pdf,.docx" hidden>
        </div>
        @error('attachment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="d-flex gap-3 mt-5">
      <button type="submit" class="btn btn-success btn-lg">
        <i class="bi bi-check-lg me-2"></i>Save
      </button>
      <a href="/incidents" class="btn btn-danger btn-lg">
        <i class="bi bi-x-lg me-2"></i>Cancel
      </a>
    </div>

  </form>
@endsection
