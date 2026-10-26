{{-- Search, sort and paging for every <table data-datatable>. Uses the theme's local copies, so no CDN is needed. --}}
@once
  @push('scripts')
    <script src="{{ asset('theme/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script>
      document.querySelectorAll('table[data-datatable]').forEach((table) => {
        new DataTable(table, {
          pageLength: 25,
          lengthMenu: [10, 25, 50, 100],
          columnDefs: table.hasAttribute('data-no-sort-last')
            ? [{ targets: -1, orderable: false, searchable: false }]
            : [],
          language: {
            search: '',
            searchPlaceholder: table.dataset.searchPlaceholder || 'Search...',
            emptyTable: table.dataset.empty || 'No records found.',
            zeroRecords: 'No matching records found.',
          },
        });
      });
    </script>
  @endpush
@endonce
