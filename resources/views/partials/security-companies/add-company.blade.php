{{-- ===== Add security company ===== --}}
<div class="modal fade" id="addCompany" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('security-companies.store') }}">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Add security company</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Company</label>
            <input name="name" class="form-control" placeholder="e.g. G4S Ghana" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact</label>
            <input name="contact" class="form-control" placeholder="e.g. +233 24 555 0111" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contract days/mo</label>
            <input name="contract_detail" class="form-control" placeholder="e.g. 30 × 42 guards" required>
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
