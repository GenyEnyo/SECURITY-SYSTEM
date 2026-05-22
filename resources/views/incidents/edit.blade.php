@extends('layouts.app')

@section('title', 'Edit Incident · M Dashboard')
@section('page', 'incidents')
@section('crumbs', '[{"label":"Incidents","href":"/incidents"},{"label":"Edit #' . $incident->id . '"}]')

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">Edit Incident #{{ $incident->id }}</h1>
      <p class="page-subtitle">Update the report details</p>
    </div>
    <div class="actions">
      <a class="btn btn-primary" href="{{ route('incidents.show', $incident) }}"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
  </div>

  <form action="{{ route('incidents.update', $incident) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">

      <div class="col-lg-6">
        <label class="field-label" for="f-type">Incident Type</label>
        <select id="f-type" name="incident_type_id" class="brand-input brand-select" required>
          @foreach ($incidentTypes as $type)
            <option value="{{ $type->id }}" @selected(old('incident_type_id', $incident->incident_type_id) == $type->id)>{{ $type->name }}</option>
          @endforeach
        </select>
        @error('incident_type_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-date">Date and Time</label>
        <div class="position-relative">
          <input id="f-date" name="occurred_at" type="datetime-local" class="brand-input"
                 value="{{ old('occurred_at', $incident->occurred_at->format('Y-m-d\TH:i')) }}" required>
          <i class="bi bi-calendar-event position-absolute" style="right:22px;top:50%;transform:translateY(-50%);font-size:24px;pointer-events:none;"></i>
        </div>
        @error('occurred_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-loc">Location</label>
        <select id="f-loc" name="location_id" class="brand-input brand-select" required>
          @foreach ($locations as $location)
            <option value="{{ $location->id }}" @selected(old('location_id', $incident->location_id) == $location->id)>{{ $location->name }}</option>
          @endforeach
        </select>
        @error('location_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-sev">Severity / Priority Level</label>
        <select id="f-sev" name="severity_id" class="brand-input brand-select" required>
          @foreach ($severities as $severity)
            <option value="{{ $severity->id }}" @selected(old('severity_id', $incident->severity_id) == $severity->id)>{{ $severity->name }}</option>
          @endforeach
        </select>
        @error('severity_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-officer">Reporting Officer</label>
        <input id="f-officer" class="brand-input readonly" value="{{ $incident->user->name }}" readonly>
      </div>

      <div class="col-12">
        <label class="field-label" for="f-desc">Description</label>
        <textarea id="f-desc" name="description" class="brand-input" rows="4" placeholder="The issue I want to report is...">{{ old('description', $incident->description) }}</textarea>
        @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-12">
        <label class="field-label">
          Upload attachment
          <span class="muted fw-5" style="font-size:14px;">(.png, .jpg, .pdf, .docx — not more than 5MB)</span>
        </label>
        @if ($incident->attachment_path)
          <div class="muted fw-5 mb-2" style="font-size:14px;">
            Current: <a href="{{ Storage::url($incident->attachment_path) }}" target="_blank">{{ basename($incident->attachment_path) }}</a> · uploading a new file will replace it.
          </div>
        @endif
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
        <i class="bi bi-check-lg me-2"></i>Update
      </button>
      <a href="{{ route('incidents.show', $incident) }}" class="btn btn-danger btn-lg">
        <i class="bi bi-x-lg me-2"></i>Cancel
      </a>
    </div>

  </form>
@endsection
