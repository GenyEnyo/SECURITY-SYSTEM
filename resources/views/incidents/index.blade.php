<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Incidents · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
</head>
<body data-page="incidents" data-crumbs='[{"label":"Incidents"}]'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>

    <div class="app-main">
      <header class="app-topbar" data-shell="topbar"></header>

      <main class="app-content">

        <!-- ============================================================
             Page header — matches the Figma frame "Incidents Management"
             Title size 36px, subtitle 16px, primary "Add" button top-right
             ============================================================ -->
        <div class="page-head">
          <div>
            <h1 class="page-title">Incidents</h1>
            <p class="page-subtitle">Manage incident reporting here</p>
          </div>
          <div class="actions">
            <a class="btn btn-primary" href="{{ route('incidents.create') }}">
              <i class="bi bi-plus-square me-2"></i>Add
            </a>
          </div>
        </div>

        @if (session('status'))
          <div class="alert alert-success" role="alert" style="background:rgba(61,179,110,.12);border:1px solid var(--brand-success);border-radius:10px;padding:12px 18px;">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
          </div>
        @endif

        <!-- ============================================================
             Toolbar: search + filter chips (right-aligned in Figma)
             ============================================================ -->
        <div class="toolbar">
          <div class="d-flex gap-2 align-items-center" style="font-size:14px;">
            <span class="muted fw-7">Filter:</span>
            <button class="btn btn-sm btn-primary">All</button>
            <button class="btn btn-sm btn-outline-primary">Open</button>
            <button class="btn btn-sm btn-outline-primary">Reviewing</button>
            <button class="btn btn-sm btn-outline-primary">Resolved</button>
          </div>
          <div class="spacer"></div>
          <div class="search-shadow">
            <i class="bi bi-search"></i>
            <input type="search" placeholder="search for incident...">
          </div>
        </div>

        <!-- ============================================================
             Data table — indigo header bar, white rows, action icons
             Columns match Figma: No. · Date · Incident Type · Location · Status · Severity · Actions
             ============================================================ -->
        <table class="brand-table">
          <thead>
            <tr>
              <th style="width:80px">No.</th>
              <th>Date</th>
              <th>Incident Type</th>
              <th>Location</th>
              <th>Status</th>
              <th>Severity</th>
              <th class="actions-col">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($occurrences as $occurrence)
              <tr>
                <td class="fw-7">{{ $occurrence->id }}</td>
                <td>{{ $occurrence->occurred_at->format('d/m/y') }}</td>
                <td>{{ $occurrence->incidentType->name }}</td>
                <td>{{ $occurrence->location->name }}</td>
                <td><span class="pill pill-{{ strtolower($occurrence->status->name) }}">{{ $occurrence->status->name }}</span></td>
                <td><span class="pill pill-{{ strtolower($occurrence->severity->name) }}">{{ $occurrence->severity->name }}</span></td>
                <td>
                  <div class="row-actions">
                    <a href="{{ route('incidents.show', $occurrence) }}" class="ra-btn" title="View"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('incidents.edit', $occurrence) }}" class="ra-btn" title="Edit"><i class="bi bi-pencil-square"></i></a>
                    <form action="{{ route('incidents.destroy', $occurrence) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this incident? This cannot be undone.');">
                      @csrf @method('DELETE')
                      <button type="submit" class="ra-btn danger" title="Delete"><i class="bi bi-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center muted py-4">
                  No incidents yet — <a href="{{ route('incidents.create') }}">add one</a>.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="mt-3">
          {{ $occurrences->links() }}
        </div>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
</body>
</html>
