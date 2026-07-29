@extends('layouts.app')

@section('title', 'Setups — Users · M Dashboard')
@section('page-title', 'Users')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Setups</a></li>
  <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')

  <!-- Toolbar: search + Add button -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="app-search">
      <input type="search" class="form-control" placeholder="search for user...">
      <i class="ti ti-search app-search-icon"></i>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUser">
      <i class="ti ti-plus me-1"></i>Add
    </button>
  </div>

  <!-- Users table — No · Name · Company · Role · Gender · Status · Actions -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:80px">No.</th>
              <th>Name</th>
              <th>Company</th>
              <th>Role</th>
              <th>Gender</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-semibold">1</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">KA</span>
                  <div>
                    <div class="fw-semibold">Kwasi Ansah</div>
                    <div class="text-muted fs-13">kwasi.ansah@company.com</div>
                  </div>
                </div>
              </td>
              <td>G4S</td>
              <td><span class="badge bg-warning-subtle text-warning">Senior Officer</span></td>
              <td>Male</td>
              <td><span class="badge bg-success-subtle text-success">Active</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-eye"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-pencil"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deleteUser"><i class="ti ti-trash"></i></button>
              </td>
            </tr>
            <tr>
              <td class="fw-semibold">2</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">GA</span>
                  <div>
                    <div class="fw-semibold">Gideon Atakli</div>
                    <div class="text-muted fs-13">gideon.atakli@company.com</div>
                  </div>
                </div>
              </td>
              <td>G4S</td>
              <td><span class="badge bg-info-subtle text-info">Supervisor</span></td>
              <td>Male</td>
              <td><span class="badge bg-success-subtle text-success">Active</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-eye"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-pencil"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deleteUser"><i class="ti ti-trash"></i></button>
              </td>
            </tr>
            <tr>
              <td class="fw-semibold">3</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="avatar-sm rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center">BA</span>
                  <div>
                    <div class="fw-semibold">Belinda Asare</div>
                    <div class="text-muted fs-13">belinda.asare@company.com</div>
                  </div>
                </div>
              </td>
              <td>Royal Knights</td>
              <td><span class="badge bg-info-subtle text-info">Supervisor</span></td>
              <td>Female</td>
              <td><span class="badge bg-success-subtle text-success">Active</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-eye"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-pencil"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deleteUser"><i class="ti ti-trash"></i></button>
              </td>
            </tr>
            <tr>
              <td class="fw-semibold">4</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">DK</span>
                  <div>
                    <div class="fw-semibold">David Kofi</div>
                    <div class="text-muted fs-13">david.kofi@company.com</div>
                  </div>
                </div>
              </td>
              <td>Starry Eagles</td>
              <td><span class="badge bg-danger-subtle text-danger">Head of Security</span></td>
              <td>Male</td>
              <td><span class="badge bg-success-subtle text-success">Active</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-eye"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-pencil"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deleteUser"><i class="ti ti-trash"></i></button>
              </td>
            </tr>
            <tr>
              <td class="fw-semibold">5</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">AM</span>
                  <div>
                    <div class="fw-semibold">Ama Mensah</div>
                    <div class="text-muted fs-13">ama.mensah@company.com</div>
                  </div>
                </div>
              </td>
              <td>G4S</td>
              <td><span class="badge bg-success-subtle text-success">Admin</span></td>
              <td>Female</td>
              <td><span class="badge bg-success-subtle text-success">Inactive</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-eye"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-secondary"><i class="ti ti-pencil"></i></button>
                <button class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deleteUser"><i class="ti ti-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (static, mock) -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">Page 1 of 10</div>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary rounded-pill">5</span>
          <span class="text-muted">rows per page</span>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item disabled"><a class="page-link" href="#">…</a></li>
            <li class="page-item"><a class="page-link" href="#">4</a></li>
            <li class="page-item"><a class="page-link" href="#">5</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Add user modal -->
  <div class="modal fade" id="addUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Invite a new user</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full name</label>
              <input class="form-control" placeholder="e.g. Kwame Boateng">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" placeholder="kwame@company.com">
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <select class="form-select">
                <option>Senior Officer</option><option>Supervisor</option>
                <option>Head of Security</option><option>Admin</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Company</label>
              <select class="form-select">
                <option>G4S</option><option>Royal Knights</option><option>Starry Eagles</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-success"><i class="ti ti-send me-1"></i>Send invite</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete user modal -->
  <div class="modal fade" id="deleteUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body p-4 text-center">
          <div class="avatar-md bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 fs-28">
            <i class="ti ti-user-off"></i>
          </div>
          <h5 class="mb-2">Remove this user?</h5>
          <p class="text-muted fw-medium mb-4">They will lose access immediately. This can be reversed by an Admin within 30 days.</p>
          <div class="d-flex gap-2 justify-content-center">
            <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-danger" data-bs-dismiss="modal">Yes, remove</button>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
