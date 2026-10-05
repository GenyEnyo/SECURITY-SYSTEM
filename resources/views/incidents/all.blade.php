@extends('layouts.app')

@section('title', 'All incidents · M Dashboard')
@section('page-title', 'All incidents')
@section('crumbs')
  <li class="breadcrumb-item">Records</li>
  <li class="breadcrumb-item active">All incidents</li>
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">All incidents <span class="text-muted fw-medium fs-13">(Head of Security view)</span></h4>
      <p class="text-muted mb-0">Every incident across all officers and locations</p>
    </div>
    <button class="btn btn-outline-primary"><i class="ti ti-file-spreadsheet me-1"></i>Export</button>
  </div>

  @php
    $statusColors = [
      'low' => 'success', 'resolved' => 'success', 'closed' => 'success',
      'reviewing' => 'info',
      'medium' => 'warning', 'reported' => 'warning',
      'urgent' => 'danger', 'escalated' => 'danger',
    ];
  @endphp

  <div id="bulk-bar" class="alert alert-primary d-none align-items-center justify-content-between mb-2">
    <div class="fw-semibold"><span id="bulk-count">0</span> selected</div>
    <div class="d-flex gap-2">
      <button class="btn btn-sm btn-warning"><i class="ti ti-arrow-up-circle me-1"></i>Escalate</button>
      <button class="btn btn-sm btn-success"><i class="ti ti-check me-1"></i>Mark resolved</button>
      <button class="btn btn-sm btn-outline-primary"><i class="ti ti-archive me-1"></i>Close</button>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-3">
        <div class="app-search">
          <input type="search" class="form-control" placeholder="search...">
          <i class="ti ti-search app-search-icon"></i>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:40px;"><input type="checkbox" id="check-all"></th>
              <th>No.</th>
              <th>Date</th>
              <th>Incident Type</th>
              <th>Location</th>
              <th>Reported by</th>
              <th>Status</th>
              <th>Severity</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="all-tbody">
            @forelse ($occurrences as $occurrence)
              @php
                $initials = collect(explode(' ', $occurrence->user->name))->map(fn ($w) => $w[0] ?? '')->take(2)->join('');
                $sc = $statusColors[strtolower($occurrence->status->name)] ?? 'secondary';
              @endphp
              <tr>
                <td><input type="checkbox" class="row-check"></td>
                <td class="fw-semibold">{{ $occurrence->id }}</td>
                <td>{{ $occurrence->occurred_at->format('d/m/y') }}</td>
                <td>{{ $occurrence->incidentType->name }}</td>
                <td>{{ $occurrence->location->name }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">{{ $initials }}</span>
                    <span class="fw-semibold">{{ $occurrence->user->name }}</span>
                  </div>
                </td>
                <td>
                  <span class="badge bg-{{ $sc }}-subtle text-{{ $sc }}">{{ $occurrence->status->name }}</span>
                  @if ($occurrence->isAcknowledged())
                    <span class="badge bg-success-subtle text-success">Acknowledged</span>
                  @endif
                </td>
                <td><span class="badge" style="background:{{ $occurrence->severity->color }}1f;color:{{ $occurrence->severity->color }};">{{ $occurrence->severity->name }}</span></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-icon btn-soft-secondary" href="{{ route('incidents.show', $occurrence) }}" title="View"><i class="ti ti-eye"></i></a>
                  @can('Acknowledge Incident')
                  @unless ($occurrence->isAcknowledged())
                    <form action="{{ route('incidents.acknowledge', $occurrence) }}" method="POST" style="display:inline;"
                          onsubmit="return confirm('Acknowledge receipt? The reporter will no longer be able to edit or delete it.');">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-icon btn-soft-secondary" title="Acknowledge receipt"><i class="ti ti-circle-check"></i></button>
                    </form>
                  @endunless
                  @endcan
                  @if (! $occurrence->isLocked())
                    @can('Edit Incident')
                    <a class="btn btn-sm btn-icon btn-soft-secondary" href="{{ route('incidents.edit', $occurrence) }}" title="Edit"><i class="ti ti-pencil"></i></a>
                    @endcan
                    @can('Delete Incident')
                    <form action="{{ route('incidents.destroy', $occurrence) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this incident?');">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-icon btn-soft-danger" title="Delete"><i class="ti ti-trash"></i></button>
                    </form>
                    @endcan
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="9" class="text-center text-muted py-4">No incidents yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">{{ $occurrences->links() }}</div>
@endsection

@push('scripts')
<script>
  const ca = document.getElementById('check-all');
  if (ca) {
    ca.addEventListener('change', () => {
      document.querySelectorAll('.row-check').forEach(c => c.checked = ca.checked);
      updateBulk();
    });
  }
  document.addEventListener('change', e => { if (e.target.matches('.row-check')) updateBulk(); });
  function updateBulk() {
    const n = document.querySelectorAll('.row-check:checked').length;
    const bar = document.getElementById('bulk-bar');
    document.getElementById('bulk-count').textContent = n;
    bar.classList.toggle('d-none', n === 0);
    bar.classList.toggle('d-flex', n > 0);
  }
</script>
@endpush
