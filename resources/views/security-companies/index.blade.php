<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Security companies · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
</head>
<body data-page="security-companies" data-crumbs='[{"label":"Setups"},{"label":"Security companies"}]'>

  <div class="app">
    <aside class="app-sidebar" data-shell="sidebar"></aside>

    <div class="app-main">
      <header class="app-topbar" data-shell="topbar"></header>

      <main class="app-content">

        <!-- Page header -->
        <div class="page-head">
          <div>
            <h1 class="page-title">Contracted Security Companies</h1>
            <p class="page-subtitle">Manage the security companies under contract</p>
          </div>
          <div class="actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompany">
              <i class="bi bi-plus-square me-2"></i>Add security company
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

        <table class="brand-table">
          <thead>
            <tr>
              <th style="width:80px">No.</th>
              <th>Company</th>
              <th>Contact</th>
              <th>Contract days/mo</th>
              <th>Status</th>
              <th class="actions-col">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($companies as $company)
              @php
                $pill = $company->status === 'renewing' ? 'pill-reviewing'
                      : ($company->status === 'inactive' ? 'pill-closed' : 'pill-resolved');
              @endphp
              <tr>
                <td class="fw-7">{{ $loop->iteration }}</td>
                <td>{{ $company->name }}</td>
                <td>{{ $company->contact }}</td>
                <td>{{ $company->contract_detail }}</td>
                <td><span class="pill {{ $pill }}">{{ ucfirst($company->status) }}</span></td>
                <td><div class="row-actions">
                  <button type="button" class="ra-btn js-edit-company"
                          data-bs-toggle="tooltip" title="Edit"
                          data-id="{{ $company->id }}"
                          data-name="{{ $company->name }}"
                          data-contact="{{ $company->contact }}"
                          data-contract="{{ $company->contract_detail }}"
                          data-status="{{ $company->status }}">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button type="button" class="ra-btn danger js-delete-company"
                          data-bs-toggle="tooltip" title="Delete"
                          data-id="{{ $company->id }}"
                          data-name="{{ $company->name }}">
                    <i class="bi bi-trash"></i>
                  </button>
                </div></td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center muted fw-5">No security companies yet.</td></tr>
            @endforelse
          </tbody>
        </table>

      </main>
    </div>
  </div>

  @include('partials.security-companies.add-company')
  @include('partials.security-companies.edit-company')
  @include('partials.security-companies.delete-company')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/partials.js"></script>
  <script src="/assets/js/app.js"></script>
  <script>
    document.addEventListener('click', (e) => {
      const t = e.target.closest('.js-edit-company, .js-delete-company');
      if (!t) return;
      const d = t.dataset;
      const open = (id, fill) => {
        const el = document.getElementById(id);
        fill(el);
        new bootstrap.Modal(el).show();
      };
      if (t.classList.contains('js-edit-company')) {
        open('editCompany', (el) => {
          el.querySelector('form').action = `/security-companies/${d.id}`;
          el.querySelector('[name="name"]').value = d.name;
          el.querySelector('[name="contact"]').value = d.contact;
          el.querySelector('[name="contract_detail"]').value = d.contract;
          el.querySelector('[name="status"]').value = d.status;
        });
      } else {
        open('deleteCompany', (el) => {
          el.querySelector('form').action = `/security-companies/${d.id}`;
          el.querySelector('.target-name').textContent = d.name;
        });
      }
    });
  </script>
</body>
</html>
