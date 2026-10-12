@extends('layouts.app')

@section('title', 'Edit User · M Dashboard')
@section('page-title', 'Edit User')
@section('crumbs')
  <li class="breadcrumb-item">Setups</li>
  <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
  <li class="breadcrumb-item active">{{ $user->name }}</li>
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

  <div class="card">
    <div class="card-header">
      <h4 class="card-title mb-0">{{ $user->name }}</h4>
      <p class="text-muted mb-0">Set where this user works and tick the roles they should hold.</p>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('users.roles.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label" for="u-location">Location</label>
            <select id="u-location" name="location_id" class="form-select">
              <option value="">No location</option>
              @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id', $user->location_id) == $location->id)>{{ $location->name }}</option>
              @endforeach
            </select>
            <div class="form-text">The user only sees data for this location.</div>
          </div>
          <div class="col-md-4">
            <label class="form-label" for="u-department">Department</label>
            <input id="u-department" name="department" class="form-control" list="u-departments" value="{{ old('department', $user->department) }}">
            <datalist id="u-departments">
              @foreach ($departments as $department)
                <option value="{{ $department }}"></option>
              @endforeach
            </datalist>
          </div>
          <div class="col-md-4">
            <label class="form-label" for="u-section">Section</label>
            <input id="u-section" name="section" class="form-control" list="u-sections" value="{{ old('section', $user->section) }}">
            <datalist id="u-sections">
              @foreach ($sections as $section)
                <option value="{{ $section }}"></option>
              @endforeach
            </datalist>
          </div>
        </div>

        <label class="form-label">Roles</label>
        <div class="row">
          @foreach ($roles as $role)
            <div class="col-md-3 mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="role[]"
                       value="{{ $role->name }}" id="role_{{ $role->id }}" @checked(in_array($role->name, $userRoles))>
                <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
              </div>
            </div>
          @endforeach
        </div>

        <div class="mt-4 d-flex gap-2">
          <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Update</button>
        </div>
      </form>
    </div>
  </div>
@endsection
