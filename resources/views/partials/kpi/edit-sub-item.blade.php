{{-- ===== Edit sub-item ===== --}}
<div class="modal fade" id="editSubItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;border:0;">
      <form method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title fw-7">Edit sub-item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="field-label"><span class="criteria-label">Criteria</span></label>
          <input name="criteria" class="brand-input" required style="height:54px;">

          <label class="field-label mt-3"><span class="target-label">Target</span></label>
          <input type="number" name="target" class="brand-input" min="0" required style="height:54px;">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-2"></i>Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
