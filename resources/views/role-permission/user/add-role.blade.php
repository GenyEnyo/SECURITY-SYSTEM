@extends('layouts.app')

@section('title', 'Assign Roles · M Dashboard')
@section('page-title', 'Assign Roles')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
  <li class="breadcrumb-item active">Assign Roles</li>
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
      <h4 class="card-title mb-0">Roles for {{ $user->name }}</h4>
      <p class="text-muted mb-0">Tick the roles this user should hold.</p>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('users.roles.update', $user) }}">
        @csrf
        @method('PUT')
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
