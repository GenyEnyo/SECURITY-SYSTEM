@extends('layouts.app')

@section('title', 'Audit Logs · M Dashboard')
@section('page-title', 'Audit Logs')
@section('crumbs')
  <li class="breadcrumb-item">Setups</li>
  <li class="breadcrumb-item active">Audit Logs</li>
@endsection

@section('content')
  @php
    // Render a logged value (scalar, null or list) as short text.
    $show = fn ($value) => is_array($value)
        ? (empty($value) ? '—' : implode(', ', array_map(fn ($v) => is_scalar($v) ? (string) $v : json_encode($v), $value)))
        : ($value === null || $value === '' ? '—' : (is_bool($value) ? ($value ? 'Yes' : 'No') : (string) $value));
  @endphp

  <div class="mb-3">
    <h4 class="mb-0">Audit Logs</h4>
    <p class="text-muted mb-0">Who did what, and when.</p>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger" role="alert">
      @foreach ($errors->all() as $message)
        <div><i class="ti ti-alert-triangle me-2"></i>{{ $message }}</div>
      @endforeach
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="GET" action="{{ route('audit.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-md-3">
          <label class="form-label" for="f-log">Area</label>
          <select id="f-log" name="log" class="form-select">
            <option value="">All areas</option>
            @foreach ($logNames as $name)
              <option value="{{ $name }}" @selected(($filters['log'] ?? '') === $name)>{{ ucfirst(str_replace('_', ' ', $name)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label" for="f-user">User</label>
          <select id="f-user" name="user" class="form-select">
            <option value="">All users</option>
            @foreach ($users as $user)
              <option value="{{ $user->id }}" @selected((int) ($filters['user'] ?? 0) === $user->id)>{{ $user->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label" for="f-from">From</label>
          <input id="f-from" type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
        </div>
        <div class="col-md-2">
          <label class="form-label" for="f-to">To</label>
          <input id="f-to" type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-grow-1"><i class="ti ti-filter me-1"></i>Filter</button>
          <a href="{{ route('audit.index') }}" class="btn btn-light">Reset</a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover table-centered mb-0">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>User</th>
              <th>Action</th>
              <th>Model</th>
              <th>Changes</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($activities as $log)
              @php
                $old = (array) ($log->properties['old'] ?? []);
                $new = (array) ($log->properties['attributes'] ?? []);
                $other = $log->properties->except(['old', 'attributes'])->all();
              @endphp
              <tr>
                <td class="text-nowrap">{{ $log->created_at->format('d M, Y H:i') }}</td>
                <td>{{ $log->causer?->name ?? 'System' }}</td>
                <td>{{ ucfirst($log->description) }}</td>
                <td class="text-nowrap">
                  @if ($log->subject_type)
                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="fs-xs">
                  @foreach ($new as $field => $value)
                    <div>
                      <span class="fw-semibold">{{ $field }}:</span>
                      @if (array_key_exists($field, $old))
                        <span class="text-muted">{{ $show($old[$field]) }}</span> &rarr;
                      @endif
                      {{ $show($value) }}
                    </div>
                  @endforeach
                  @foreach ($other as $field => $value)
                    <div><span class="fw-semibold">{{ $field }}:</span> {{ $show($value) }}</div>
                  @endforeach
                  @if (! $new && ! $other)
                    <span class="text-muted">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted py-4">No audit entries found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">{{ $activities->links('pagination::bootstrap-5') }}</div>
    </div>
  </div>
@endsection
