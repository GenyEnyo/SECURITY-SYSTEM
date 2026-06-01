@extends('layouts.app')

@section('title', 'KPI settings · M Dashboard')
@section('page', 'kpi-settings')
@section('crumbs', '[{"label":"Setups"},{"label":"KPI settings"}]')

@push('head')
  <link href="/assets/css/extras.css" rel="stylesheet">
@endpush

@section('content')
  <div class="page-head">
    <div>
      <h1 class="page-title">KPI Settings</h1>
      <p class="page-subtitle">Configure KPI groups and sub-items used for daily scorecards</p>
    </div>
    <div class="actions">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroup">
        <i class="bi bi-plus-square me-2"></i>Add KPI group
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

  @forelse ($groups as $group)
    <section class="kpi-group" data-group>
      <header>
        <div class="d-flex align-items-center gap-2">
          <div>
            <h4>{{ $group->name }}</h4>
            <div class="muted fw-5" style="font-size:12px;">
              Weight {{ $group->weight }}% · {{ $group->subItems->count() }} sub-item{{ $group->subItems->count() === 1 ? '' : 's' }}
            </div>
          </div>
        </div>
        <div class="row-actions">
          <button type="button" class="ra-btn js-edit-group"
                  data-bs-toggle="tooltip" title="Edit group"
                  data-id="{{ $group->id }}"
                  data-name="{{ $group->name }}"
                  data-weight="{{ $group->weight }}">
            <i class="bi bi-pencil-square"></i>
          </button>
          <button type="button" class="ra-btn danger js-delete-group"
                  data-bs-toggle="tooltip" title="Delete group"
                  data-id="{{ $group->id }}"
                  data-name="{{ $group->name }}">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </header>
      <div class="body">
        @if ($group->subItems->isEmpty())
          <p class="muted fw-5 mb-3" style="font-size:13px;">No sub-items yet.</p>
        @else
          <table class="kpi-table">
            <thead>
              <tr>
                <th>{{ $group->criteria_label }}</th>
                <th style="width:120px">{{ $group->target_label }}</th>
                <th class="actions-col" style="width:120px">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($group->subItems as $item)
                <tr>
                  <td>{{ $item->criteria }}</td>
                  <td>{{ $item->target }}</td>
                  <td>
                    <div class="row-actions">
                      <button type="button" class="ra-btn js-edit-sub-item"
                              data-bs-toggle="tooltip" title="Edit"
                              data-id="{{ $item->id }}"
                              data-criteria="{{ $item->criteria }}"
                              data-target="{{ $item->target }}"
                              data-criteria-label="{{ $group->criteria_label }}"
                              data-target-label="{{ $group->target_label }}">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button type="button" class="ra-btn danger js-delete-sub-item"
                              data-bs-toggle="tooltip" title="Delete"
                              data-id="{{ $item->id }}"
                              data-criteria="{{ $item->criteria }}">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
        <button type="button" class="btn btn-outline-primary btn-sm mt-3"
                onclick="openAddSubItem({{ $group->id }}, '{{ addslashes($group->name) }}', '{{ addslashes($group->criteria_label) }}', '{{ addslashes($group->target_label) }}')">
          <i class="bi bi-plus-square me-2"></i>Add sub-item
        </button>
      </div>
    </section>
  @empty
    <div class="muted fw-5" style="padding:32px;text-align:center;border:1px dashed var(--border-soft);border-radius:12px;">
      No KPI groups yet. Click <strong>Add KPI group</strong> to create your first one.
    </div>
  @endforelse

  @include('partials.kpi.add-group')
  @include('partials.kpi.edit-group')
  @include('partials.kpi.delete-group')
  @include('partials.kpi.add-sub-item')
  @include('partials.kpi.edit-sub-item')
  @include('partials.kpi.delete-sub-item')
@endsection

@push('scripts')
  <script>
    // Accordion toggle — preserves the existing UX.
    document.querySelectorAll('.kpi-group > header').forEach(h => {
      h.addEventListener('click', e => {
        if (e.target.closest('.ra-btn,.form-check,input')) return;
        h.parentElement.classList.toggle('collapsed');
      });
    });

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
