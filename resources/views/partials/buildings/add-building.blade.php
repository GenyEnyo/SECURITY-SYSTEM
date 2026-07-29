{{-- ===== Add building ===== --}}
<div class="modal fade" id="addBuilding" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('buildings.store') }}">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Add a building</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Building name</label>
            <input name="name" class="form-control" placeholder="e.g. Akosombo" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Location</label>
            <select name="location_id" class="form-select" required>
              <option value="" disabled selected>Select a location...</option>
              @foreach ($locations as $loc)
                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
              @endforeach
            </select>
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
