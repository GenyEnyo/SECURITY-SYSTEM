{{-- ===== Add sub-item ===== --}}
<div class="modal fade" id="addSubItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Add sub-item</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-3">Group: <span class="target-name fw-semibold"></span></p>

          <div class="mb-3">
            <label class="form-label"><span class="criteria-label">Criteria</span></label>
            <input name="criteria" class="form-control" placeholder="e.g. Guards reporting on time" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label"><span class="target-label">Target</span></label>
            <input type="number" name="target" class="form-control" min="0" value="0" required>
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
