@extends('layouts.app')

@section('title', 'Security companies · M Dashboard')
@section('page-title', 'Security companies')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Setups</a></li>
  <li class="breadcrumb-item active">Security companies</li>
@endsection

@section('content')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Contracted Security Companies</h4>
      <p class="text-muted mb-0">Manage the security companies under contract</p>
    </div>
    @can('Manage Security Companies')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompany">
      <i class="ti ti-plus me-1"></i>Add security company
    </button>
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
        <table class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:80px">No.</th>
              <th>Company</th>
              <th>Contact</th>
              <th>Contract days/mo</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($companies as $company)
              @php
                $badge = $company->status === 'renewing' ? 'bg-info-subtle text-info'
                       : ($company->status === 'inactive' ? 'bg-success-subtle text-success' : 'bg-success-subtle text-success');
              @endphp
              <tr>
                <td class="fw-semibold">{{ $loop->iteration }}</td>
                <td>{{ $company->name }}</td>
                <td>{{ $company->contact }}</td>
                <td>{{ $company->contract_detail }}</td>
                <td><span class="badge {{ $badge }}">{{ ucfirst($company->status) }}</span></td>
                <td class="text-end">
                  @can('Manage Security Companies')
                  <button type="button" class="btn btn-sm btn-icon btn-soft-secondary js-edit-company"
                          data-bs-toggle="tooltip" title="Edit"
                          data-id="{{ $company->id }}"
                          data-name="{{ $company->name }}"
                          data-contact="{{ $company->contact }}"
                          data-contract="{{ $company->contract_detail }}"
                          data-status="{{ $company->status }}">
                    <i class="ti ti-pencil"></i>
                  </button>
                  @endcan
                  @can('Manage Security Companies')
                  <button type="button" class="btn btn-sm btn-icon btn-soft-danger js-delete-company"
                          data-bs-toggle="tooltip" title="Delete"
                          data-id="{{ $company->id }}"
                          data-name="{{ $company->name }}">
                    <i class="ti ti-trash"></i>
                  </button>
                  @endcan
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted fw-medium">No security companies yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @include('partials.security-companies.add-company')
  @include('partials.security-companies.edit-company')
  @include('partials.security-companies.delete-company')

@endsection

@push('scripts')
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
@endpush
