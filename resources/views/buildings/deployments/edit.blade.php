@extends('layouts.app')

@section('title', 'Edit Deployment · M Dashboard')
@section('page', 'locations')
@section('crumbs', json_encode([
    ['label' => 'Setups'],
    ['label' => 'Locations', 'href' => '/locations'],
    ['label' => $building->name, 'href' => '/buildings/' . $building->id . '/deployments'],
    ['label' => 'Edit #' . $deployment->id],
]))

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">Edit Deployment #{{ $deployment->id }}</h1>
      <p class="page-subtitle">Update deployment details for {{ $building->name }}</p>
    </div>
    <div class="actions">
      <a class="btn btn-primary" href="{{ route('buildings.deployments.show', [$building, $deployment]) }}">
        <i class="bi bi-arrow-left me-2"></i>Back
      </a>
    </div>
  </div>

  <form action="{{ route('buildings.deployments.update', [$building, $deployment]) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">

      <div class="col-lg-6">
        <label class="field-label" for="f-company">Security Company</label>
        <select id="f-company" name="security_company_id" class="brand-input brand-select" required>
          <option value="" disabled>Select company...</option>
          @foreach ($companies as $company)
            <option value="{{ $company->id }}" @selected(old('security_company_id', $deployment->security_company_id) == $company->id)>{{ $company->name }}</option>
          @endforeach
        </select>
        @error('security_company_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-shift">Shift</label>
        <select id="f-shift" name="shift_id" class="brand-input brand-select" required>
          <option value="" disabled>Select shift...</option>
          @foreach ($shifts as $shift)
            <option value="{{ $shift->id }}" @selected(old('shift_id', $deployment->shift_id) == $shift->id)>{{ $shift->name }}</option>
          @endforeach
        </select>
        @error('shift_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-building">Office / Building / Asset</label>
        <input id="f-building" class="brand-input readonly" value="{{ $building->name }}" readonly>
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-loc">Location</label>
        <input id="f-loc" class="brand-input readonly" value="{{ $building->location->name }}" readonly>
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-officer">Supervising Officer</label>
        <select id="f-officer" name="supervising_officer" class="brand-input brand-select" required>
          <option value="" disabled>Select officer...</option>
          @foreach ($officers as $officer)
            <option value="{{ $officer }}" @selected(old('supervising_officer', $deployment->supervising_officer) === $officer)>{{ $officer }}</option>
          @endforeach
        </select>
        @error('supervising_officer') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-guards">Number of Guards</label>
        <input id="f-guards" type="number" name="number_of_guards" class="brand-input" min="1"
               value="{{ old('number_of_guards', $deployment->number_of_guards) }}" required>
        @error('number_of_guards') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-start">Start Date and Time</label>
        <input id="f-start" type="datetime-local" name="start_at" class="brand-input"
               value="{{ old('start_at', $deployment->start_at->format('Y-m-d\TH:i')) }}" required>
        @error('start_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-lg-6">
        <label class="field-label" for="f-end">End Date and Time</label>
        <input id="f-end" type="datetime-local" name="end_at" class="brand-input"
               value="{{ old('end_at', $deployment->end_at->format('Y-m-d\TH:i')) }}" required>
        @error('end_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-12">
        <label class="field-label" for="f-notes">Deployment Notes</label>
        <textarea id="f-notes" name="notes" class="brand-input" rows="4" placeholder="Any notes about this deployment...">{{ old('notes', $deployment->notes) }}</textarea>
        @error('notes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

    </div>

    <div class="d-flex gap-3 mt-5">
      <button type="submit" class="btn btn-success btn-lg">
        <i class="bi bi-check-lg me-2"></i>Update
      </button>
      <a href="{{ route('buildings.deployments.show', [$building, $deployment]) }}" class="btn btn-danger btn-lg">
        <i class="bi bi-x-lg me-2"></i>Cancel
      </a>
    </div>

  </form>
@endsection
