<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>All incidents · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
</head>
<body data-page="all-incidents" data-crumbs='[{"label":"Records"},{"label":"All incidents"}]'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>
    <div class="app-main">
      <header class="app-topbar" data-shell="topbar"></header>
      <main class="app-content">

        <div class="page-head">
          <div>
            <h1 class="page-title">All incidents <span class="muted fw-5" style="font-size:13px;">(Head of Security view)</span></h1>
            <p class="page-subtitle">Every incident across all officers and locations</p>
          </div>
          <div class="actions">
            <button class="btn btn-outline-primary"><i class="bi bi-file-earmark-excel me-2"></i>Export</button>
          </div>
        </div>

        <!-- Filter pills -->
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

        <!-- Bulk actions when checked -->
        <div id="bulk-bar" class="alert d-none align-items-center justify-content-between mb-2"
             style="background:var(--brand-primary-soft); border:1px solid var(--brand-primary); padding:10px 14px; border-radius:8px;">
          <div class="fw-7" style="font-size:13px;"><span id="bulk-count">0</span> selected</div>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-warning"><i class="bi bi-arrow-up-circle me-1"></i>Escalate</button>
            <button class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Mark resolved</button>
            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-archive me-1"></i>Close</button>
          </div>
        </div>

        <!-- Table — includes Reported by column -->
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
                <td><span class="pill pill-{{ strtolower($occurrence->severity->name) }}">{{ $occurrence->severity->name }}</span></td>
                <td>
                  <div class="row-actions">
                    <a class="ra-btn" href="{{ route('incidents.show', $occurrence) }}"><i class="bi bi-eye"></i></a>
                    <a class="ra-btn" href="{{ route('incidents.edit', $occurrence) }}"><i class="bi bi-pencil-square"></i></a>
                    <form action="{{ route('incidents.destroy', $occurrence) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this incident?');">
                      @csrf @method('DELETE')
                      <button type="submit" class="ra-btn danger"><i class="bi bi-trash"></i></button>
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

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
  <script>
    // Bulk select wiring (no backend handler yet — UI only)
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
</body>
</html>
