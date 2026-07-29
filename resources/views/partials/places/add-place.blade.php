{{-- ===== Add specific location ===== --}}
<div class="modal fade" id="addPlace" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('buildings.places.store', $building) }}">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Add specific location</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Specific location name</label>
            <input name="name" class="form-control" placeholder="e.g. Main Gate" required autofocus>
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
