{{-- ===== Delete specific location ===== --}}
<div class="modal fade" id="deletePlace" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;border:0;">
      <form method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body p-4 text-center">
          <div class="mx-auto mb-3" style="width:60px;height:60px;border-radius:50%;background:rgba(252,51,32,.12);color:var(--brand-danger);display:grid;place-items:center;font-size:28px;">
            <i class="bi bi-trash"></i>
          </div>
          <h5 class="fw-7 mb-2">Delete this specific location?</h5>
          <p class="muted fw-5 mb-4">"<span class="target-name fw-7"></span>" will be removed permanently.</p>
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Yes, delete</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
