@extends('layouts.app')

@section('title', 'KPI settings · M Dashboard')
@section('page-title', 'KPI settings')
@section('crumbs')
  <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Setups</a></li>
  <li class="breadcrumb-item active">KPI settings</li>
@endsection

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="mb-0">KPI Settings</h4>
      <p class="text-muted mb-0">Configure KPI groups and sub-items used for daily scorecards</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroup">
      <i class="ti ti-plus me-1"></i>Add KPI group
    </button>
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

  <div class="accordion" id="kpiSettingsGroups">
    @forelse ($groups as $group)
      @php
        $isDeployment = strtolower(trim($group->name)) === 'deployment';
        $gIdx = $loop->index;
      @endphp
      <div class="accordion-item kpi-group" data-group>
        <h2 class="accordion-header d-flex align-items-center">
          <button class="accordion-button flex-grow-1" type="button" data-bs-toggle="collapse"
                  data-bs-target="#kpiSet{{ $gIdx }}" aria-expanded="true" aria-controls="kpiSet{{ $gIdx }}">
            <div>
              <span class="fw-semibold d-block">{{ $group->name }}</span>
              <small class="text-muted">
                @if ($isDeployment)
                  Weight {{ $group->weight }}% · estimates by location &amp; building
                @else
                  Weight {{ $group->weight }}% · {{ $group->subItems->count() }} sub-item{{ $group->subItems->count() === 1 ? '' : 's' }}
                @endif
              </small>
            </div>
          </button>
          <div class="d-flex gap-1 px-3">
            <button type="button" class="btn btn-sm btn-icon btn-soft-secondary js-edit-group"
                    title="Edit group"
                    data-id="{{ $group->id }}"
                    data-name="{{ $group->name }}"
                    data-weight="{{ $group->weight }}">
              <i class="ti ti-pencil"></i>
            </button>
            <button type="button" class="btn btn-sm btn-icon btn-soft-danger js-delete-group"
                    title="Delete group"
                    data-id="{{ $group->id }}"
                    data-name="{{ $group->name }}">
              <i class="ti ti-trash"></i>
            </button>
          </div>
        </h2>
        <div id="kpiSet{{ $gIdx }}" class="accordion-collapse collapse show">
          <div class="accordion-body">
            @if ($isDeployment)
              @include('partials.kpi.deployment-estimates')
            @else
              @if ($group->subItems->isEmpty())
                <p class="text-muted mb-3 small">No sub-items yet.</p>
              @else
                <div class="table-responsive">
                  <table class="kpi-table table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>{{ $group->criteria_label }}</th>
                        <th style="width:120px">{{ $group->target_label }}</th>
                        <th class="text-end" style="width:120px">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($group->subItems as $item)
                        <tr>
                          <td>{{ $item->criteria }}</td>
                          <td>{{ $item->target }}</td>
                          <td class="text-end">
                            <button type="button" class="btn btn-sm btn-icon btn-soft-secondary js-edit-sub-item"
                                    title="Edit"
                                    data-id="{{ $item->id }}"
                                    data-criteria="{{ $item->criteria }}"
                                    data-target="{{ $item->target }}"
                                    data-criteria-label="{{ $group->criteria_label }}"
                                    data-target-label="{{ $group->target_label }}">
                              <i class="ti ti-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-icon btn-soft-danger js-delete-sub-item"
                                    title="Delete"
                                    data-id="{{ $item->id }}"
                                    data-criteria="{{ $item->criteria }}">
                              <i class="ti ti-trash"></i>
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
              <button type="button" class="btn btn-outline-primary btn-sm mt-3"
                      onclick="openAddSubItem({{ $group->id }}, '{{ addslashes($group->name) }}', '{{ addslashes($group->criteria_label) }}', '{{ addslashes($group->target_label) }}')">
                <i class="ti ti-plus me-1"></i>Add sub-item
              </button>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="card">
        <div class="card-body text-center text-muted py-5">
          No KPI groups yet. Click <strong>Add KPI group</strong> to create your first one.
        </div>
      </div>
    @endforelse
  </div>

  @include('partials.kpi.add-group')
  @include('partials.kpi.edit-group')
  @include('partials.kpi.delete-group')
  @include('partials.kpi.add-sub-item')
  @include('partials.kpi.edit-sub-item')
  @include('partials.kpi.delete-sub-item')
@endsection

@push('scripts')
  <script>
    window.openAddSubItem = (groupId, groupName, criteriaLabel, targetLabel) => {
      const el = document.getElementById('addSubItem');
      el.querySelector('form').action = `/kpi/groups/${groupId}/sub-items`;
      el.querySelector('.target-name').textContent = groupName;
      el.querySelector('.criteria-label').textContent = criteriaLabel || 'Criteria';
      el.querySelector('.target-label').textContent = targetLabel || 'Target';
      el.querySelector('[name="criteria"]').value = '';
      el.querySelector('[name="target"]').value = 0;
      new bootstrap.Modal(el).show();
    };

    document.addEventListener('click', (e) => {
      const t = e.target.closest(
        '.js-edit-group, .js-delete-group, .js-edit-sub-item, .js-delete-sub-item'
      );
      if (!t) return;
      e.stopPropagation();

      const d = t.dataset;
      const open = (modalId, fill) => {
        const el = document.getElementById(modalId);
        fill(el);
        new bootstrap.Modal(el).show();
      };

      if (t.classList.contains('js-edit-group')) {
        open('editGroup', (el) => {
          el.querySelector('form').action = `/kpi/groups/${d.id}`;
          el.querySelector('[name="name"]').value = d.name;
          el.querySelector('[name="weight"]').value = d.weight;
        });
      } else if (t.classList.contains('js-delete-group')) {
        open('deleteGroup', (el) => {
          el.querySelector('form').action = `/kpi/groups/${d.id}`;
          el.querySelector('.target-name').textContent = d.name;
        });
      } else if (t.classList.contains('js-edit-sub-item')) {
        open('editSubItem', (el) => {
          el.querySelector('form').action = `/kpi/sub-items/${d.id}`;
          el.querySelector('.criteria-label').textContent = d.criteriaLabel || 'Criteria';
          el.querySelector('.target-label').textContent = d.targetLabel || 'Target';
          el.querySelector('[name="criteria"]').value = d.criteria;
          el.querySelector('[name="target"]').value = d.target;
        });
      } else if (t.classList.contains('js-delete-sub-item')) {
        open('deleteSubItem', (el) => {
          el.querySelector('form').action = `/kpi/sub-items/${d.id}`;
          el.querySelector('.target-name').textContent = d.criteria;
        });
      }
    });
  </script>
@endpush
