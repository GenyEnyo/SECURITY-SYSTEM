@extends('layouts.app')

@section('title', 'All incidents · M Dashboard')
@section('page', 'all-incidents')
@section('crumbs', '[{"label":"Records"},{"label":"All incidents"}]')

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">All incidents <span class="muted fw-5" style="font-size:13px;">(Head of Security view)</span></h1>
      <p class="page-subtitle">Every incident across all officers and locations</p>
    </div>
    <div class="actions">
      <button class="btn btn-outline-primary"><i class="bi bi-file-earmark-excel me-2"></i>Export</button>
    </div>
  </div>

  <div class="toolbar">
    <div class="d-flex gap-2 align-items-center" style="font-size:13px;">
      <span class="muted fw-7">Status:</span>
      <button class="btn btn-sm btn-primary">All</button>
      <button class="btn btn-sm btn-outline-primary">Reported</button>
      <button class="btn btn-sm btn-outline-primary">Reviewing</button>
      <button class="btn btn-sm btn-outline-primary">Escalated</button>
      <button class="btn btn-sm btn-outline-primary">Resolved</button>
      <button class="btn btn-sm btn-outline-primary">Closed</button>
    </div>
    <div class="spacer"></div>
    <div class="search-shadow"><i class="bi bi-search"></i><input placeholder="search...">
    </div>
  </div>

  <div id="bulk-bar" class="alert d-none align-items-center justify-content-between mb-2"
       style="background:var(--brand-primary-soft); border:1px solid var(--brand-primary); padding:10px 14px; border-radius:8px;">
    <div class="fw-7" style="font-size:13px;"><span id="bulk-count">0</span> selected</div>
    <div class="d-flex gap-2">
      <button class="btn btn-sm btn-warning"><i class="bi bi-arrow-up-circle me-1"></i>Escalate</button>
      <button class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Mark resolved</button>
      <button class="btn btn-sm btn-outline-primary"><i class="bi bi-archive me-1"></i>Close</button>
    </div>
  </div>

  <table class="brand-table">
    <thead>
      <tr>
        <th style="width:40px;"><input type="checkbox" id="check-all"></th>
        <th>No.</th>
        <th>Date</th>
        <th>Incident Type</th>
        <th>Location</th>
        <th>Reported by</th>
        <th>Status</th>
        <th>Severity</th>
        <th class="actions-col">Actions</th>
      </tr>
    </thead>
    <tbody id="all-tbody">
      @forelse ($occurrences as $occurrence)
        @php $initials = collect(explode(' ', $occurrence->user->name))->map(fn ($w) => $w[0] ?? '')->take(2)->join(''); @endphp
        <tr>
          <td><input type="checkbox" class="row-check"></td>
          <td class="fw-7">{{ $occurrence->id }}</td>
          <td>{{ $occurrence->occurred_at->format('d/m/y') }}</td>
          <td>{{ $occurrence->incidentType->name }}</td>
          <td>{{ $occurrence->location->name }}</td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar" style="width:24px;height:24px;font-size:10px;">{{ $initials }}</div>
              <span class="fw-7" style="font-size:12.5px;">{{ $occurrence->user->name }}</span>
            </div>
          </td>
          <td><span class="pill pill-{{ strtolower($occurrence->status->name) }}">{{ $occurrence->status->name }}</span></td>
          <td><span class="pill" style="background:{{ $occurrence->severity->color }}1f;color:{{ $occurrence->severity->color }};">{{ $occurrence->severity->name }}</span></td>
          <td>
            <div class="row-actions">
              <a class="ra-btn" href="{{ route('incidents.show', $occurrence) }}" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></a>
              <a class="ra-btn" href="{{ route('incidents.edit', $occurrence) }}" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil-square"></i></a>
              <form action="{{ route('incidents.destroy', $occurrence) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this incident?');">
                @csrf @method('DELETE')
                <button type="submit" class="ra-btn danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center muted py-4">No incidents yet.</td></tr>
      @endforelse
    </tbody>
  </table>

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
