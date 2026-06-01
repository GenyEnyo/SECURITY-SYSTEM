<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Locations · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
</head>
<body data-page="locations" data-crumbs='[{"label":"Setups"},{"label":"Locations"}]'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>

    <div class="app-main">
      <header class="app-topbar" data-shell="topbar"></header>

      <main class="app-content">

        <!-- Page header -->
        <div class="page-head">
          <div>
            <h1 class="page-title">Locations &amp; Company Management</h1>
            <p class="page-subtitle">Manage security company deployments here</p>
          </div>
          <div class="actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBuilding">
              <i class="bi bi-plus-square me-2"></i>Add building
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

        <!-- Tile grid -->
        <div class="row g-3">
          @forelse ($buildings as $building)
            <div class="col-md-6 col-lg-4 col-xl-3">
              <div class="location-tile">
                <div class="row-actions" style="position:absolute;top:8px;right:8px;">
                  <button type="button" class="ra-btn js-edit-building"
                          data-bs-toggle="tooltip" title="Edit"
                          data-id="{{ $building->id }}"
                          data-name="{{ $building->name }}"
                          data-location-id="{{ $building->location_id }}">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button type="button" class="ra-btn danger js-delete-building"
                          data-bs-toggle="tooltip" title="Delete"
                          data-id="{{ $building->id }}"
                          data-name="{{ $building->name }}">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
                <div>
                  <div class="title">{{ $building->name }}</div>
                  <div class="sub muted mt-2">{{ $building->location->name }}</div>
                </div>
                <a href="{{ route('buildings.deployments.index', $building) }}" class="btn btn-primary">Manage</a>
              </div>
            </div>
          @empty
          @endforelse

          <!-- Add new tile -->
          <div class="col-md-6 col-lg-4 col-xl-3">
            <button type="button" class="location-tile add-tile" data-bs-toggle="modal" data-bs-target="#addBuilding" style="width:100%;">
              <i class="bi bi-plus-circle"></i>
              <div class="fw-7" style="font-size:18px;">Add new building</div>
              <div class="muted fw-5">Posts &amp; sites</div>
            </button>
          </div>
        </div>

      </main>
    </div>
  </div>

  @include('partials.buildings.add-building')
  @include('partials.buildings.edit-building')
  @include('partials.buildings.delete-building')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
  <script>
    document.addEventListener('click', (e) => {
      const t = e.target.closest('.js-edit-building, .js-delete-building');
      if (!t) return;
      const d = t.dataset;
      const open = (id, fill) => {
        const el = document.getElementById(id);
        fill(el);
        new bootstrap.Modal(el).show();
      };
      if (t.classList.contains('js-edit-building')) {
        open('editBuilding', (el) => {
          el.querySelector('form').action = `/buildings/${d.id}`;
          el.querySelector('[name="name"]').value = d.name;
          el.querySelector('[name="location_id"]').value = d.locationId;
        });
      } else {
        open('deleteBuilding', (el) => {
          el.querySelector('form').action = `/buildings/${d.id}`;
          el.querySelector('.target-name').textContent = d.name;
        });
      }
    });
  </script>
</body>
</html>
