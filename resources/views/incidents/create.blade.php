<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Add Incident · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
</head>
<body data-page="add-incident" data-crumbs='[{"label":"Incidents","href":"/incidents"},{"label":"Add"}]'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>

    <div class="app-main">
      <header class="app-topbar" data-shell="topbar"></header>

      <main class="app-content">

        <!-- ============================================================
             Page head — title 36px, "Back" button top right (matches Figma)
             ============================================================ -->
        <div class="page-head">
          <div>
            <h1 class="page-title">Add Incident</h1>
            <p class="page-subtitle">Report incidents on this form</p>
          </div>
          <div class="actions">
            <a class="btn btn-primary" href="/incidents"><i class="bi bi-arrow-left me-2"></i>Back</a>
          </div>
        </div>

        <!-- ============================================================
             Form — two-column grid (Bootstrap row/col), 82px tall inputs,
             dropzone for attachments, save (green) + cancel (red) bottom-left.
             ============================================================ -->
        <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="row g-4">

            <!-- Incident type -->
            <div class="col-lg-6">
              <label class="field-label" for="f-type">Incident Type</label>
              <select id="f-type" name="incident_type_id" class="brand-input brand-select" required>
                <option value="" disabled @selected(! old('incident_type_id'))>Select incident type...</option>
                @foreach ($incidentTypes as $type)
                  <option value="{{ $type->id }}" @selected(old('incident_type_id') == $type->id)>{{ $type->name }}</option>
                @endforeach
              </select>
              @error('incident_type_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Date and Time -->
            <div class="col-lg-6">
              <label class="field-label" for="f-date">Date and Time</label>
              <div class="position-relative">
                <input id="f-date" name="occurred_at" type="datetime-local" class="brand-input" value="{{ old('occurred_at') }}" required>
                <i class="bi bi-calendar-event position-absolute" style="right:22px;top:50%;transform:translateY(-50%);font-size:24px;pointer-events:none;"></i>
              </div>
              @error('occurred_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Location -->
            <div class="col-lg-6">
              <label class="field-label" for="f-loc">Location</label>
              <select id="f-loc" name="location_id" class="brand-input brand-select" required>
                <option value="" disabled @selected(! old('location_id'))>Select your location...</option>
                @foreach ($locations as $location)
                  <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
                @endforeach
              </select>
              @error('location_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Severity / Priority -->
            <div class="col-lg-6">
              <label class="field-label" for="f-sev">Severity / Priority Level</label>
              <select id="f-sev" name="severity_id" class="brand-input brand-select" required>
                <option value="" disabled @selected(! old('severity_id'))>Select priority level...</option>
                @foreach ($severities as $severity)
                  <option value="{{ $severity->id }}" @selected(old('severity_id') == $severity->id)>{{ $severity->name }}</option>
                @endforeach
              </select>
              @error('severity_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Reporting Officer — AUTO-FILLED, read-only (server overwrites this) -->
            <div class="col-lg-6">
              <label class="field-label" for="f-officer">Reporting Officer</label>
              <input id="f-officer" class="brand-input readonly" value="Kwasi Ansah" readonly>
            </div>

            <!-- Status — default Reported -->
            {{-- <div class="col-lg-6">
              <label class="field-label" for="f-status">Status</label>
              <select id="f-status" name="incident_status_id" class="brand-input brand-select">
                @foreach ($statuses as $status)
                  <option value="{{ $status->id }}" @selected(old('incident_status_id', $statuses->firstWhere('name', 'Reported')?->id) == $status->id)>{{ $status->name }}</option>
                @endforeach
              </select>
              @error('incident_status_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div> --}}

            <!-- Description -->
            <div class="col-12">
              <label class="field-label" for="f-desc">Description</label>
              <textarea id="f-desc" name="description" class="brand-input" rows="4" placeholder="The issue I want to report is...">{{ old('description') }}</textarea>
              @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Attachment -->
            <div class="col-12">
              <label class="field-label">Upload attachment <span class="muted fw-5" style="font-size:14px;">(.png, .jpg, .pdf, .docx — not more than 5MB)</span></label>
              <div class="brand-dropzone">
                <i class="bi bi-cloud-arrow-up"></i>
                <div class="dz-label">
                  <div class="fw-7" style="font-size:16px;">Drop a file here, or click to browse</div>
                  <div class="muted fw-5" style="font-size:14px;">Max 5MB · PNG, JPG, PDF, DOCX</div>
                </div>
                <input type="file" name="attachment" accept=".png,.jpg,.jpeg,.pdf,.docx" hidden>
              </div>
              @error('attachment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
          </div>

          <!-- Action row -->
          <div class="d-flex gap-3 mt-5">
            <button type="submit" class="btn btn-success btn-lg">
              <i class="bi bi-check-lg me-2"></i>Save
            </button>
            <a href="/incidents" class="btn btn-danger btn-lg">
              <i class="bi bi-x-lg me-2"></i>Cancel
            </a>
          </div>

        </form>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
</body>
</html>
