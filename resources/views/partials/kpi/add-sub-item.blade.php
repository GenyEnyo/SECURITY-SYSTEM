{{-- ===== Add sub-item ===== --}}
<div class="modal fade" id="addSubItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;border:0;">
      <form method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title fw-7">Add sub-item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="muted fw-5 mb-3" style="font-size:13px;">Group: <span class="target-name fw-7"></span></p>

          <label class="field-label"><span class="criteria-label">Criteria</span></label>
          <input name="criteria" class="brand-input" placeholder="e.g. Guards reporting on time" required autofocus style="height:54px;">

          <label class="field-label mt-3"><span class="target-label">Target</span></label>
          <input type="number" name="target" class="brand-input" min="0" value="0" required style="height:54px;">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-2"></i>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
