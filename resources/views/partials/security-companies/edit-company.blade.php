{{-- ===== Edit security company ===== --}}
<div class="modal fade" id="editCompany" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h4 class="modal-title">Edit security company</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Company</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact</label>
            <input name="contact" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contract days/mo</label>
            <input name="contract_detail" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="active">Active</option>
              <option value="renewing">Renewing</option>
              <option value="inactive">Inactive</option>
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
