/* ---------------------------------------------------------------------
   M Dashboard — light interactivity (drop-in JS)
   - File upload preview for the dropzone
   - Toast helper window.mNotify(msg, type)
   --------------------------------------------------------------------- */

(function () {
  // Wire all .brand-dropzone areas to their nearest hidden file input
  document.addEventListener('click', e => {
    const dz = e.target.closest('.brand-dropzone');
    if (!dz) return;
    const input = dz.querySelector('input[type=file]') || dz.parentElement.querySelector('input[type=file]');
    if (input) input.click();
  });

  document.addEventListener('change', e => {
    if (!e.target.matches('input[type=file]')) return;
    const dz = e.target.closest('.brand-dropzone') || e.target.previousElementSibling;
    if (!dz) return;
    const f = e.target.files && e.target.files[0];
    if (f) {
      const label = dz.querySelector('.dz-label') || dz;
      label.innerHTML = `<i class="bi bi-file-earmark-check-fill" style="color:var(--brand-success)"></i>
                         <div class="fw-7">${f.name}</div>
                         <div class="muted" style="font-size:14px">${(f.size / 1024).toFixed(1)} KB · click to replace</div>`;
    }
  });

  // Tiny toast helper
  window.mNotify = function (msg, type = 'info') {
    let wrap = document.getElementById('m-toast-wrap');
    if (!wrap) {
      wrap = document.createElement('div');
      wrap.id = 'm-toast-wrap';
      wrap.style.cssText = 'position:fixed;top:80px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:9999';
      document.body.appendChild(wrap);
    }
    const colors = {
      success: '#3DB36E', info: '#081f39', warning: '#FFA91F', danger: '#FC3320'
    };
    const el = document.createElement('div');
    el.style.cssText = `background:#fff;border-left:6px solid ${colors[type] || colors.info};
                        padding:14px 22px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.18);
                        font-weight:700;min-width:260px`;
    el.textContent = msg;
    wrap.appendChild(el);
    setTimeout(() => el.style.opacity = '0', 2600);
    setTimeout(() => el.remove(), 3000);
  };
})();
