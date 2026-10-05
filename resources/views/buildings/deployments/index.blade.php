@extends('layouts.app')

@section('title', 'Manage ' . $building->name . ' · M Dashboard')
@section('page-title', 'Manage ' . $building->name)
@section('crumbs')
  <li class="breadcrumb-item">Setups</li>
  <li class="breadcrumb-item"><a href="{{ url('/deployments') }}">Deployments</a></li>
  <li class="breadcrumb-item active">{{ $building->name }}</li>
@endsection

@push('head')
  <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Manage {{ $building->name }}</h4>
      <p class="text-muted mb-0">{{ $building->location->name }} — Deployments for this building</p>
    </div>
    @can('Manage Deployments')
    <a class="btn btn-primary" href="{{ route('buildings.deployments.create', $building) }}">
      <i class="ti ti-plus me-1"></i>Add Deployment
    </a>
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
        <table id="deployments-table" class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th>Shift</th>
              <th>Date</th>
              <th>Guard No.</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th class="text-end">Actions</th>
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
                <td class="text-end">
                  <a href="{{ route('buildings.deployments.show', [$building, $d]) }}"
                     class="btn btn-sm btn-icon btn-soft-secondary" title="View">
                    <i class="ti ti-eye"></i>
                  </a>
                  @can('Manage Deployments')
                  <a href="{{ route('buildings.deployments.edit', [$building, $d]) }}"
                     class="btn btn-sm btn-icon btn-soft-secondary" title="Edit">
                    <i class="ti ti-pencil"></i>
                  </a>
                  @endcan
                  @can('Manage Deployments')
                  <button type="button" class="btn btn-sm btn-icon btn-soft-danger js-delete-deployment"
                          title="Delete"
                          data-id="{{ $d->id }}"
                          data-label="{{ $d->shift->name }} on {{ $d->start_at->format('Y-m-d') }}">
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
