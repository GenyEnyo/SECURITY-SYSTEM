@extends('layouts.app')

@section('title', 'Manage ' . $building->name . ' · M Dashboard')
@section('page', 'locations')
@section('crumbs', json_encode([
    ['label' => 'Setups'],
    ['label' => 'Locations', 'href' => '/locations'],
    ['label' => $building->name],
]))

@push('head')
  <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">Manage {{ $building->name }}</h1>
      <p class="page-subtitle">{{ $building->location->name }} — Specific locations in this building</p>
    </div>
    <div class="actions">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPlace">
        <i class="bi bi-plus-square me-2"></i>Add specific location
      </button>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success" role="alert" style="background:rgba(61,179,110,.12);border:1px solid var(--brand-success);border-radius:10px;padding:12px 18px;">
      <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger" role="alert" style="background:rgba(252,51,32,.10);border:1px solid var(--brand-danger);border-radius:10px;padding:12px 18px;">
      @foreach ($errors->all() as $message)
        <div><i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}</div>
      @endforeach
    </div>
  @endif

  <table id="places-table" class="brand-table">
    <thead>
      <tr>
        <th style="width:80px">No.</th>
        <th>Name</th>
        <th class="actions-col">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($places as $place)
        <tr>
          <td class="fw-7">{{ $loop->iteration }}</td>
          <td>{{ $place->name }}</td>
          <td>
            <div class="row-actions">
              <button type="button" class="ra-btn js-edit-place"
                      data-bs-toggle="tooltip" title="Edit"
                      data-id="{{ $place->id }}"
                      data-name="{{ $place->name }}">
                <i class="bi bi-pencil-square"></i>
              </button>
              <button type="button" class="ra-btn danger js-delete-place"
                      data-bs-toggle="tooltip" title="Delete"
                      data-id="{{ $place->id }}"
                      data-name="{{ $place->name }}">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

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
