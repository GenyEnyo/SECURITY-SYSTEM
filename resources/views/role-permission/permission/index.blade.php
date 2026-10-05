@extends('layouts.app')

@section('title', 'Permissions · M Dashboard')
@section('page-title', 'Permissions')
@section('crumbs')
  <li class="breadcrumb-item">Setups</li>
  <li class="breadcrumb-item active">Permissions</li>
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Permissions</h4>
      <p class="text-muted mb-0">Manage the permissions used to control access.</p>
    </div>
    @can('Create Permission')
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItem">
        <i class="ti ti-plus me-1"></i>Add
      </button>
    @endcan
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
              <th>Name</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($permissions as $permission)
              <tr>
                <td class="fw-semibold">{{ $loop->iteration }}</td>
                <td>{{ $permission->name }}</td>
                <td class="text-end">
                  @can('Edit Permission')
                    <button type="button" class="btn btn-sm btn-icon btn-soft-secondary js-edit-item" title="Rename"
                            data-id="{{ $permission->id }}" data-name="{{ $permission->name }}">
                      <i class="ti ti-pencil"></i>
                    </button>
                  @endcan
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add modal -->
  <div class="modal fade" id="addItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('permissions.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h4 class="modal-title">Add permission</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">Name</label>
            <input name="permission_name" class="form-control" required autofocus>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit modal -->
  <div class="modal fade" id="editItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h4 class="modal-title">Rename permission</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">Name</label>
            <input name="permission_name" class="form-control" required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('click', e => {
      const t = e.target.closest('.js-edit-item');
      if (!t) return;
      const el = document.getElementById('editItem');
      el.querySelector('form').action = `{{ url('/permissions') }}/${t.dataset.id}`;
      el.querySelector('[name="permission_name"]').value = t.dataset.name;
      new bootstrap.Modal(el).show();
    });
  </script>
@endpush
