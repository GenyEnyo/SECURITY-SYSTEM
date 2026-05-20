<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Incident #{{ $incident->id }} · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
</head>
<body data-page="incidents" data-crumbs='[{"label":"Incidents","href":"/incidents"},{"label":"#{{ $incident->id }}"}]'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>

    <div class="app-main">
      <header class="app-topbar" data-shell="topbar"></header>

      <main class="app-content">

        <div class="page-head">
          <div>
            <h1 class="page-title">Incident #{{ $incident->id }}</h1>
            <p class="page-subtitle">{{ $incident->incidentType->name }} · {{ $incident->occurred_at->format('d M Y, H:i') }}</p>
          </div>
          <div class="actions d-flex gap-2">
            <a class="btn btn-primary" href="{{ route('incidents.index') }}"><i class="bi bi-arrow-left me-2"></i>Back</a>
            <a class="btn btn-warning" href="{{ route('incidents.edit', $incident) }}"><i class="bi bi-pencil-square me-2"></i>Edit</a>
            <form action="{{ route('incidents.destroy', $incident) }}" method="POST" onsubmit="return confirm('Delete this incident?');">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Delete</button>
            </form>
          </div>
        </div>

        <div class="shadow-card p-4">
          <dl class="row g-3 mb-0" style="font-size:15px;">
            <dt class="col-sm-3 muted fw-7">Incident type</dt>
            <dd class="col-sm-9">{{ $incident->incidentType->name }}</dd>

            <dt class="col-sm-3 muted fw-7">Date &amp; time</dt>
            <dd class="col-sm-9">{{ $incident->occurred_at->format('d M Y, H:i') }}</dd>

            <dt class="col-sm-3 muted fw-7">Location</dt>
            <dd class="col-sm-9">{{ $incident->location->name }}</dd>

            <dt class="col-sm-3 muted fw-7">Severity</dt>
            <dd class="col-sm-9"><span class="pill pill-{{ strtolower($incident->severity->name) }}">{{ $incident->severity->name }}</span></dd>

            <dt class="col-sm-3 muted fw-7">Status</dt>
            <dd class="col-sm-9"><span class="pill pill-{{ strtolower($incident->status->name) }}">{{ $incident->status->name }}</span></dd>

            <dt class="col-sm-3 muted fw-7">Reported by</dt>
            <dd class="col-sm-9">{{ $incident->user->name }}</dd>

            <dt class="col-sm-3 muted fw-7">Description</dt>
            <dd class="col-sm-9">{{ $incident->description ?: '—' }}</dd>

            <dt class="col-sm-3 muted fw-7">Attachment</dt>
            <dd class="col-sm-9">
              @if ($incident->attachment_path)
                <a href="{{ Storage::url($incident->attachment_path) }}" target="_blank" rel="noopener">
                  <i class="bi bi-paperclip me-1"></i>{{ basename($incident->attachment_path) }}
                </a>
              @else
                —
              @endif
            </dd>
          </dl>
        </div>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
</body>
</html>
