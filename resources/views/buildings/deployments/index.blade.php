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
      <p class="page-subtitle">{{ $building->location->name }} — Deployments for this building</p>
    </div>
    <div class="actions">
      <a class="btn btn-primary" href="{{ route('buildings.deployments.create', $building) }}">
        <i class="bi bi-plus-square me-2"></i>Add Deployment
      </a>
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

  <table id="deployments-table" class="brand-table">
    <thead>
      <tr>
        <th>Shift</th>
        <th>Date</th>
        <th>Guard No.</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th class="actions-col">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($deployments as $d)
        <tr>
          <td>{{ $d->shift->name }}</td>
          <td>{{ $d->start_at->format('Y-m-d') }}</td>
          <td>{{ $d->number_of_guards }}</td>
          <td>{{ $d->start_at->format('H:i') }}</td>
          <td>{{ $d->end_at->format('H:i') }}</td>
          <td>
            <div class="row-actions">
              <a href="{{ route('buildings.deployments.show', [$building, $d]) }}"
                 class="ra-btn" data-bs-toggle="tooltip" title="View">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('buildings.deployments.edit', [$building, $d]) }}"
                 class="ra-btn" data-bs-toggle="tooltip" title="Edit">
                <i class="bi bi-pencil-square"></i>
              </a>
              <button type="button" class="ra-btn danger js-delete-deployment"
                      data-bs-toggle="tooltip" title="Delete"
                      data-id="{{ $d->id }}"
                      data-label="{{ $d->shift->name }} on {{ $d->start_at->format('Y-m-d') }}">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  @include('partials.deployments.delete-deployment')
@endsection

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $('#deployments-table').DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      columnDefs: [{ targets: -1, orderable: false, searchable: false }],
      language: { search: '', searchPlaceholder: 'search deployments...' },
    });

    document.addEventListener('click', (e) => {
      const t = e.target.closest('.js-delete-deployment');
      if (!t) return;
      const buildingId = {{ $building->id }};
      const el = document.getElementById('deleteDeployment');
      el.querySelector('form').action = `/buildings/${buildingId}/deployments/${t.dataset.id}`;
      el.querySelector('.target-name').textContent = t.dataset.label;
      new bootstrap.Modal(el).show();
    });
  </script>
@endpush
