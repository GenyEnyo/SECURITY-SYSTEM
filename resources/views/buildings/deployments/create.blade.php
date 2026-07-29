@extends('layouts.app')

@section('title', 'Add Deployment · M Dashboard')
@section('page-title', 'Add Deployment')
@section('crumbs')
  <li class="breadcrumb-item">Setups</li>
  <li class="breadcrumb-item"><a href="{{ url('/locations') }}">Locations</a></li>
  <li class="breadcrumb-item"><a href="{{ url('/buildings/' . $building->id . '/deployments') }}">{{ $building->name }}</a></li>
  <li class="breadcrumb-item active">Add Deployment</li>
@endsection

@section('content')
  <div class="row justify-content-center">
    <div class="col-xxl-10">

      <form action="{{ route('buildings.deployments.store', $building) }}" method="POST">
        @csrf

        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Add Deployment</h4>
            <a class="btn btn-sm btn-soft-primary" href="{{ route('buildings.deployments.index', $building) }}">
              <i class="ti ti-arrow-left me-1"></i>Back
            </a>
          </div>

          <div class="card-body">
            <p class="text-muted">Assign guards and shifts for {{ $building->name }}.</p>

            <div class="row g-3">

              <div class="col-lg-6">
                <label class="form-label" for="f-company">Security Company</label>
                <select id="f-company" name="security_company_id" class="form-select @error('security_company_id') is-invalid @enderror" required>
                  <option value="" disabled @selected(! old('security_company_id'))>Select company...</option>
                  @foreach ($companies as $company)
                    <option value="{{ $company->id }}" @selected(old('security_company_id') == $company->id)>{{ $company->name }}</option>
                  @endforeach
                </select>
                @error('security_company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-shift">Shift</label>
                <select id="f-shift" name="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required>
                  <option value="" disabled @selected(! old('shift_id'))>Select shift...</option>
                  @foreach ($shifts as $shift)
                    <option value="{{ $shift->id }}" @selected(old('shift_id') == $shift->id)>{{ $shift->name }}</option>
                  @endforeach
                </select>
                @error('shift_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-building">Office / Building / Asset</label>
                <input id="f-building" class="form-control" value="{{ $building->name }}" readonly>
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-loc">Location</label>
                <input id="f-loc" class="form-control" value="{{ $building->location->name }}" readonly>
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-officer">Supervising Officer</label>
                <select id="f-officer" name="supervising_officer" class="form-select @error('supervising_officer') is-invalid @enderror" required>
                  <option value="" disabled @selected(! old('supervising_officer'))>Select officer...</option>
                  @foreach ($officers as $officer)
                    <option value="{{ $officer }}" @selected(old('supervising_officer') === $officer)>{{ $officer }}</option>
                  @endforeach
                </select>
                @error('supervising_officer') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-guards">Number of Guards</label>
                <input id="f-guards" type="number" name="number_of_guards" class="form-control @error('number_of_guards') is-invalid @enderror" min="1" value="{{ old('number_of_guards') }}" required>
                @error('number_of_guards') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-start">Start Date and Time</label>
                <input id="f-start" type="datetime-local" name="start_at" class="form-control @error('start_at') is-invalid @enderror" value="{{ old('start_at') }}" required>
                @error('start_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="f-end">End Date and Time</label>
                <input id="f-end" type="datetime-local" name="end_at" class="form-control @error('end_at') is-invalid @enderror" value="{{ old('end_at') }}" required>
                @error('end_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-12">
                <label class="form-label" for="f-notes">Deployment Notes</label>
                <textarea id="f-notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="Any notes about this deployment...">{{ old('notes') }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

            </div>
          </div>

          <div class="card-footer d-flex gap-2">
            <button type="submit" class="btn btn-success">
              <i class="ti ti-check me-1"></i>Save
            </button>
            <a href="{{ route('buildings.deployments.index', $building) }}" class="btn btn-light">
              <i class="ti ti-x me-1"></i>Cancel
            </a>
          </div>
        </div>
      </form>

    </div>
  </div>
@endsection
