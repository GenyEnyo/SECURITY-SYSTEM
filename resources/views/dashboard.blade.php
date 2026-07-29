@extends('layouts.app')

@section('title', 'Dashboard · M Dashboard')
@section('page-title', 'Dashboard')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
  <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

  <!-- KPI alert banner -->
  <div class="alert alert-warning d-flex align-items-center justify-content-between" role="alert">
    <div class="d-flex align-items-center gap-3">
      <i class="ti ti-alert-octagon fs-22"></i>
      <div>
        <div class="fw-semibold fs-16">KPI scorecard not yet submitted</div>
        <div class="text-muted fw-medium">Submit today's scorecard before 18:00 to avoid a late entry flag.</div>
      </div>
    </div>
    <a href="#" class="btn btn-warning"><i class="ti ti-edit me-1"></i>Submit now</a>
  </div>

  <!-- Metric cards -->
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">42</h4>
              <p class="text-muted mb-0">Contracted today</p>
            </div>
            <div class="avatar-md bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-users"></i>
            </div>
          </div>
          <small class="badge bg-success-subtle text-success">2 vs. last shift</small>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">38</h4>
              <p class="text-muted mb-0">Actual deployed</p>
            </div>
            <div class="avatar-md bg-success-subtle text-success rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-circle-check"></i>
            </div>
          </div>
          <small class="badge bg-danger-subtle text-danger">4 short</small>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">90.5%</h4>
              <p class="text-muted mb-0">Deployment %</p>
            </div>
            <div class="avatar-md bg-warning-subtle text-warning rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-chart-arrows"></i>
            </div>
          </div>
          <small class="badge bg-danger-subtle text-danger">Target 100%</small>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-1">5</h4>
              <p class="text-muted mb-0">Incidents this week</p>
            </div>
            <div class="avatar-md bg-danger-subtle text-danger rounded d-flex align-items-center justify-content-center fs-22">
              <i class="ti ti-shield-x"></i>
            </div>
          </div>
          <small class="badge bg-danger-subtle text-danger">2 vs. last week</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Two-column: recent incidents + week chart -->
  <div class="row">
    <!-- Recent incidents -->
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h4 class="card-title mb-0">Recent incidents</h4>
          <a href="/incidents" class="fw-semibold fs-14">View all <i class="ti ti-arrow-right"></i></a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-centered mb-0">
              <thead class="table-light">
                <tr><th>Date</th><th>Type</th><th>Location</th><th>Severity</th><th>Status</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td>05/11/26</td><td>Item Loss</td><td>Akuse</td>
                  <td><span class="badge bg-success-subtle text-success">Low</span></td>
                  <td><span class="badge bg-info-subtle text-info">Reviewing</span></td>
                </tr>
                <tr>
                  <td>23/10/26</td><td>Injury</td><td>Accra</td>
                  <td><span class="badge bg-success-subtle text-success">Low</span></td>
                  <td><span class="badge bg-success-subtle text-success">Resolved</span></td>
                </tr>
                <tr>
                  <td>10/10/26</td><td>Damage</td><td>Aboadze</td>
                  <td><span class="badge bg-warning-subtle text-warning">Medium</span></td>
                  <td><span class="badge bg-warning-subtle text-warning">Reported</span></td>
                </tr>
                <tr>
                  <td>14/09/26</td><td>Theft</td><td>Akosombo</td>
                  <td><span class="badge bg-danger-subtle text-danger">Urgent</span></td>
                  <td><span class="badge bg-danger-subtle text-danger">Escalated</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Week chart -->
    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h4 class="card-title mb-0">Incidents this week</h4>
          <span class="text-muted fw-semibold fs-13">Mon — Sun</span>
        </div>
        <div class="card-body">
          <div style="display:flex;align-items:flex-end;gap:14px;height:200px;padding:10px 0;">
            <!-- Each bar -->
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
              <div class="bg-primary-subtle" style="width:100%;height:30%;border-radius:6px 6px 0 0;"></div>
              <div class="text-muted fw-semibold fs-12">Mon</div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
              <div class="bg-primary" style="width:100%;height:50%;border-radius:6px 6px 0 0;"></div>
              <div class="text-muted fw-semibold fs-12">Tue</div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
              <div class="bg-primary-subtle" style="width:100%;height:15%;border-radius:6px 6px 0 0;"></div>
              <div class="text-muted fw-semibold fs-12">Wed</div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
              <div class="bg-warning" style="width:100%;height:80%;border-radius:6px 6px 0 0;"></div>
              <div class="text-muted fw-semibold fs-12">Thu</div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
              <div class="bg-primary" style="width:100%;height:40%;border-radius:6px 6px 0 0;"></div>
              <div class="text-muted fw-semibold fs-12">Fri</div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
              <div class="bg-primary-subtle" style="width:100%;height:25%;border-radius:6px 6px 0 0;"></div>
              <div class="text-muted fw-semibold fs-12">Sat</div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
              <div class="bg-danger" style="width:100%;height:60%;border-radius:6px 6px 0 0;"></div>
              <div class="text-muted fw-semibold fs-12">Sun</div>
            </div>
          </div>
          <div class="d-flex gap-3 mt-3 fs-12">
            <div class="d-flex align-items-center gap-2"><span class="bg-primary" style="width:10px;height:10px;border-radius:2px;"></span> Normal</div>
            <div class="d-flex align-items-center gap-2"><span class="bg-warning" style="width:10px;height:10px;border-radius:2px;"></span> Elevated</div>
            <div class="d-flex align-items-center gap-2"><span class="bg-danger" style="width:10px;height:10px;border-radius:2px;"></span> Critical</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick actions row -->
  <h4 class="mt-4 mb-2">Quick actions</h4>
  <div class="row">
    <div class="col-sm-6 col-lg-3">
      <a href="/incidents/create" class="card text-decoration-none text-body">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar-md bg-danger-subtle text-danger rounded d-flex align-items-center justify-content-center fs-22">
            <i class="ti ti-shield-plus"></i>
          </div>
          <div>
            <div class="fw-semibold">Report incident</div>
            <div class="text-muted fw-medium fs-13">New entry form</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-lg-3">
      <a href="#" class="card text-decoration-none text-body">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar-md bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center fs-22">
            <i class="ti ti-clipboard-check"></i>
          </div>
          <div>
            <div class="fw-semibold">Deployment log</div>
            <div class="text-muted fw-medium fs-13">Today's attendance</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-lg-3">
      <a href="#" class="card text-decoration-none text-body">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar-md bg-warning-subtle text-warning rounded d-flex align-items-center justify-content-center fs-22">
            <i class="ti ti-stars"></i>
          </div>
          <div>
            <div class="fw-semibold">Submit KPI scorecard</div>
            <div class="text-muted fw-medium fs-13">Daily form</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-lg-3">
      <a href="#" class="card text-decoration-none text-body">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar-md bg-success-subtle text-success rounded d-flex align-items-center justify-content-center fs-22">
            <i class="ti ti-history"></i>
          </div>
          <div>
            <div class="fw-semibold">My submissions</div>
            <div class="text-muted fw-medium fs-13">History &amp; exports</div>
          </div>
        </div>
      </a>
    </div>
  </div>

@endsection
