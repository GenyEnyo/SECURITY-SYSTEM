@extends('layouts.app')

@section('title', 'Role Permissions · M Dashboard')
@section('page-title', 'Role Permissions')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
  <li class="breadcrumb-item active">Role Permissions</li>
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
      <h4 class="card-title mb-0">Permissions for the {{ $role->name }} role</h4>
      <p class="text-muted mb-0">Tick the checkbox to add a permission to this role.</p>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('roles.permissions.update', $role) }}">
        @csrf
        @method('PUT')
        <div class="row">
          @foreach ($permissions as $permission)
            <div class="col-md-3 mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permission[]"
                       value="{{ $permission->name }}" id="permission_{{ $permission->id }}" @checked(in_array($permission->id, $rolePermissions))>
                <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
              </div>
            </div>
          @endforeach
        </div>

        <div class="mt-4 d-flex gap-2">
          <a href="{{ route('roles.index') }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Update</button>
        </div>
      </form>
    </div>
  </div>
@endsection
