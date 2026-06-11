{{-- ===== Edit specific location ===== --}}
<div class="modal fade" id="editPlace" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;border:0;">
      <form method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title fw-7">Edit specific location</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="field-label">Specific location name</label>
            <input name="name" class="brand-input" required style="height:60px;font-size:16px;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-2"></i>Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
