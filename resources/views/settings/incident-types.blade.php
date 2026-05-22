@extends('layouts.app')

@section('title', 'Settings — Incident Types · M Dashboard')
@section('page', 'incident-types')
@section('crumbs', '[{"label":"Settings"},{"label":"Incident Types"}]')

@push('head')
  <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">Settings — Incident Types</h1>
      <p class="page-subtitle">Manage the list of incident types that can be reported.</p>
    </div>
    <div class="actions">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addType">
        <i class="bi bi-plus-square me-2"></i>Add
      </button>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success" role="alert" style="background:rgba(61,179,110,.12);border:1px solid var(--brand-success);border-radius:10px;padding:12px 18px;">
      <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger" role="alert" style="background:rgba(252,51,32,.10);border:1px solid var(--brand-danger);border-radius:10px;padding:12px 18px;">
      <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger" role="alert" style="background:rgba(252,51,32,.10);border:1px solid var(--brand-danger);border-radius:10px;padding:12px 18px;">
      @foreach ($errors->all() as $message)
        <div><i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}</div>
      @endforeach
    </div>
  @endif

  <table id="types-table" class="brand-table">
    <thead>
      <tr>
        <th style="width:80px">No.</th>
        <th>Name</th>
        <th class="actions-col">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($types as $type)
        <tr>
          <td class="fw-7">{{ $type->id }}</td>
          <td>{{ $type->name }}</td>
          <td>
            <div class="row-actions">
              <button class="ra-btn"
                      onclick="editType({{ $type->id }}, '{{ addslashes($type->name) }}')"
                      data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil-square"></i></button>
              <button class="ra-btn danger"
                      onclick="deleteType({{ $type->id }}, '{{ addslashes($type->name) }}')"
                      data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <!-- Add modal -->
  <div class="modal fade" id="addType" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:14px;border:0;">
        <form action="{{ route('settings.incident-types.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title fw-7">Add incident type</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="field-label">Name</label>
            <input name="name" class="brand-input" placeholder="e.g. Vandalism" required autofocus style="height:54px;">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-2"></i>Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit modal -->
  <div class="modal fade" id="editType" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:14px;border:0;">
        <form method="POST">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title fw-7">Edit incident type</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="field-label">Name</label>
            <input name="name" class="brand-input" required style="height:54px;">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-2"></i>Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete modal -->
  <div class="modal fade" id="deleteType" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:14px;border:0;">
        <form method="POST">
          @csrf
          @method('DELETE')
          <div class="modal-body p-4 text-center">
            <div class="mx-auto mb-3" style="width:60px;height:60px;border-radius:50%;background:rgba(252,51,32,.12);color:var(--brand-danger);display:grid;place-items:center;font-size:28px;">
              <i class="bi bi-trash"></i>
            </div>
            <h5 class="fw-7 mb-2">Delete this incident type?</h5>
            <p class="muted fw-5 mb-4">"<span class="target-name fw-7"></span>"Are you sure you want to delete this Incident Type?.</p>
            <div class="d-flex gap-2 justify-content-center">
              <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
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
    window.editType = (id, name) => {
      const el = document.getElementById('editType');
      el.querySelector('form').action = `/settings/incident-types/${id}`;
      el.querySelector('[name="name"]').value = name;
      new bootstrap.Modal(el).show();
    };
    window.deleteType = (id, name) => {
      const el = document.getElementById('deleteType');
      el.querySelector('form').action = `/settings/incident-types/${id}`;
      el.querySelector('.target-name').textContent = name;
      new bootstrap.Modal(el).show();
    };

    $('#types-table').DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      columnDefs: [{ targets: -1, orderable: false, searchable: false }],
      language: { search: '', searchPlaceholder: 'search for incident type...' },
    });
  </script>
@endpush
