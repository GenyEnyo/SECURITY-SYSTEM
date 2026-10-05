@extends('layouts.app')

@section('title', 'Users · M Dashboard')
@section('page-title', 'Users')
@section('crumbs')
  <li class="breadcrumb-item">Setups</li>
  <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
  <div class="mb-3">
    <h4 class="mb-0">Users</h4>
    <p class="text-muted mb-0">Everyone who has signed in, and the roles they hold. Directory users appear here after their first sign-in.</p>
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
      <div class="table-responsive">
        <table class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:80px">No.</th>
              <th>Full name</th>
              <th>Username</th>
              <th>Email</th>
              <th>Source</th>
              <th>Roles</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($users as $user)
              <tr>
                <td class="fw-semibold">{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->username ?? '—' }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  <span class="badge {{ $user->guid ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' }}">{{ $user->guid ? 'Directory' : 'Local' }}</span>
                </td>
                <td>
                  @forelse ($user->roles as $role)
                    <span class="badge bg-success-subtle text-success">{{ $role->name }}</span>
                  @empty
                    <span class="text-muted">No role</span>
                  @endforelse
                </td>
                <td class="text-end">
                  @can('Assign Roles')
                    <a href="{{ route('users.roles.edit', $user) }}" class="btn btn-sm btn-soft-secondary">
                      <i class="ti ti-user-shield me-1"></i>Add / edit roles
                    </a>
                  @endcan
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted py-4">No users yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
