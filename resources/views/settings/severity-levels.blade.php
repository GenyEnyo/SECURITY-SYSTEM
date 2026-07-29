@extends('layouts.app')

@section('title', 'Settings — Severity Levels · M Dashboard')
@section('page-title', 'Settings — Severity Levels')
@section('crumbs')
  <li class="breadcrumb-item">Settings</li>
  <li class="breadcrumb-item active">Severity Levels</li>
@endsection

@push('head')
  <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Settings — Severity Levels</h4>
      <p class="text-muted mb-0">Manage the severity / priority levels available on incidents.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSeverity">
      <i class="ti ti-plus me-1"></i>Add
    </button>
  </div>

  @if (session('status'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
      <i class="ti ti-circle-check me-2 fs-lg"></i>{{ session('status') }}
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger d-flex align-items-center" role="alert">
      <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
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
        <table id="severities-table" class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:80px">No.</th>
              <th>Name</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($severities as $severity)
              <tr>
                <td class="fw-semibold">{{ $severity->id }}</td>
                <td><span class="badge" style="background:{{ $severity->color }}1f;color:{{ $severity->color }};">{{ $severity->name }}</span></td>
                <td class="text-end">
                  <button class="btn btn-sm btn-icon btn-soft-secondary"
                          onclick="editSeverity({{ $severity->id }}, '{{ addslashes($severity->name) }}', '{{ $severity->color }}')"
                          title="Edit"><i class="ti ti-pencil"></i></button>
                  <button class="btn btn-sm btn-icon btn-soft-danger"
                          onclick="deleteSeverity({{ $severity->id }}, '{{ addslashes($severity->name) }}')"
                          title="Delete"><i class="ti ti-trash"></i></button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add modal -->
  <div class="modal fade" id="addSeverity" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('settings.severity-levels.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h4 class="modal-title">Add severity level</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" placeholder="e.g. Critical" required autofocus>

            <label class="form-label mt-3">Badge color</label>
            <input type="color" name="color" value="#6b7280" required class="form-control form-control-color">
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
  <div class="modal fade" id="editSeverity" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h4 class="modal-title">Edit severity level</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" required>

            <label class="form-label mt-3">Badge color</label>
            <input type="color" name="color" required class="form-control form-control-color">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete modal -->
  <div class="modal fade" id="deleteSeverity" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST">
          @csrf
          @method('DELETE')
          <div class="modal-body p-4 text-center">
            <span class="avatar-md d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle fs-24 mb-3">
              <i class="ti ti-trash"></i>
            </span>
            <h4 class="mb-2">Delete this severity level?</h4>
            <p class="text-muted mb-4">"<span class="target-name fw-semibold"></span>" will be removed permanently. Existing incidents that reference it cannot be deleted.</p>
            <div class="d-flex gap-2 justify-content-center">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-danger">Yes, delete</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
  <script>
    window.editSeverity = (id, name, color) => {
      const el = document.getElementById('editSeverity');
      el.querySelector('form').action = `/settings/severity-levels/${id}`;
      el.querySelector('[name="name"]').value = name;
      el.querySelector('[name="color"]').value = color;
      new bootstrap.Modal(el).show();
    };
    window.deleteSeverity = (id, name) => {
      const el = document.getElementById('deleteSeverity');
      el.querySelector('form').action = `/settings/severity-levels/${id}`;
      el.querySelector('.target-name').textContent = name;
      new bootstrap.Modal(el).show();
    };

    $('#severities-table').DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      columnDefs: [{ targets: -1, orderable: false, searchable: false }],
      language: { search: '', searchPlaceholder: 'search for severity...' },
    });
  </script>
@endpush
