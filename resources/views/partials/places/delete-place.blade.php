{{-- ===== Delete specific location ===== --}}
<div class="modal fade" id="deletePlace" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h4 class="modal-title">Delete specific location</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div class="avatar-md mx-auto mb-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle fs-24">
            <i class="ti ti-trash"></i>
          </div>
          <h4 class="mb-2">Delete this specific location?</h4>
          <p class="text-muted mb-0">"<span class="target-name fw-semibold"></span>" will be removed permanently.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
