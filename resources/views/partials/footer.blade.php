<footer class="footer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6 text-center text-md-start">
        <script>document.write(new Date().getFullYear())</script>
        © M Dashboard — <span class="fw-bold">Security Operations</span>
      </div>
      <div class="col-md-6">
        <div class="d-none d-md-flex justify-content-end gap-3">
          <a href="{{ url('/dashboard') }}" class="link-reset">Dashboard</a>
          <a href="{{ url('/incidents') }}" class="link-reset">Incidents</a>
          <a href="{{ url('/kpi/reports') }}" class="link-reset">Reports</a>
        </div>
      </div>
    </div>
  </div>
</footer>
