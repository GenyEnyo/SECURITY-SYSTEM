@extends('layouts.app')

@section('title', 'Manage ' . $building->name . ' · M Dashboard')
@section('page-title', 'Manage ' . $building->name)
@section('crumbs')
  <li class="breadcrumb-item">Setups</li>
  <li class="breadcrumb-item"><a href="{{ url('/locations') }}">Locations</a></li>
  <li class="breadcrumb-item active">{{ $building->name }}</li>
@endsection

@push('head')
  <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Manage {{ $building->name }}</h4>
      <p class="text-muted mb-0">{{ $building->location->name }} — Specific locations in this building</p>
    </div>
    @can('Manage Locations')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPlace">
      <i class="ti ti-plus me-1"></i>Add specific location
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
        <table id="places-table" class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:80px">No.</th>
              <th>Name</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($places as $place)
              <tr>
                <td class="fw-semibold">{{ $loop->iteration }}</td>
                <td>{{ $place->name }}</td>
                <td class="text-end">
                  @can('Manage Locations')
                  <button type="button" class="btn btn-sm btn-icon btn-soft-secondary js-edit-place"
                          title="Edit"
                          data-id="{{ $place->id }}"
                          data-name="{{ $place->name }}">
                    <i class="ti ti-pencil"></i>
                  </button>
                  @endcan
                  @can('Manage Locations')
                  <button type="button" class="btn btn-sm btn-icon btn-soft-danger js-delete-place"
                          title="Delete"
                          data-id="{{ $place->id }}"
                          data-name="{{ $place->name }}">
                    <i class="ti ti-trash"></i>
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

  @include('partials.places.add-place')
  @include('partials.places.edit-place')
  @include('partials.places.delete-place')
@endsection

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $('#places-table').DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      columnDefs: [{ targets: -1, orderable: false, searchable: false }],
      language: { search: '', searchPlaceholder: 'search specific locations...' },
    });

    const buildingId = {{ $building->id }};

    document.addEventListener('click', (e) => {
      const t = e.target.closest('.js-edit-place, .js-delete-place');
      if (!t) return;
      const d = t.dataset;
      if (t.classList.contains('js-edit-place')) {
        const el = document.getElementById('editPlace');
        el.querySelector('form').action = `/buildings/${buildingId}/places/${d.id}`;
        el.querySelector('[name="name"]').value = d.name;
        new bootstrap.Modal(el).show();
      } else {
        const el = document.getElementById('deletePlace');
        el.querySelector('form').action = `/buildings/${buildingId}/places/${d.id}`;
        el.querySelector('.target-name').textContent = d.name;
        new bootstrap.Modal(el).show();
      }
    });
  </script>
@endpush
