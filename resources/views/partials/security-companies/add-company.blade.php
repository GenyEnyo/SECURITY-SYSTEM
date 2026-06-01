{{-- ===== Add security company ===== --}}
<div class="modal fade" id="addCompany" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;border:0;">
      <form method="POST" action="{{ route('security-companies.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title fw-7">Add security company</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="field-label">Company</label>
            <input name="name" class="brand-input" placeholder="e.g. G4S Ghana" required style="height:60px;font-size:16px;">
          </div>
          <div class="mb-3">
            <label class="field-label">Contact</label>
            <input name="contact" class="brand-input" placeholder="e.g. +233 24 555 0111" required style="height:60px;font-size:16px;">
          </div>
          <div class="mb-3">
            <label class="field-label">Contract days/mo</label>
            <input name="contract_detail" class="brand-input" placeholder="e.g. 30 × 42 guards" required style="height:60px;font-size:16px;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-2"></i>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
