{{-- ===== Add group ===== --}}
<div class="modal fade" id="addGroup" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('kpi.groups.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Add KPI group</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Group name</label>
            <input name="name" class="form-control" placeholder="e.g. Attendance & Punctuality" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Weight (% of overall scorecard)</label>
            <input type="number" name="weight" class="form-control" min="0" max="100" value="0" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
