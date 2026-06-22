{{--
  ============================================================================
  MONTHLY KPI REPORT  —  printable A4 page
  ============================================================================
  This view is given the following data by KpiReportController@monthly:

    $month       The chosen month as text, e.g. "2026-06"
    $monthLabel  The same month, nicely formatted, e.g. "June 2026"
    $filters     What the user filtered by: ['location_id' => .., 'building_id' => ..]
    $locations   All locations  (used to fill the Location dropdown)
    $buildings   All buildings  (used to fill the Building dropdown)
    $rollup      The month TOTALS, an array with:
                   'count'        how many scorecards were filed
                   'overall_pct'  the average overall score (0–100)
                   'groups'       one row per KPI group (name, weight, attainment)
                   'best'         the best-scoring group   (or null if no data)
                   'worst'        the worst-scoring group  (or null if no data)
    $reports     ONE item per scorecard. Each item is an array:
                   'card'       the scorecard (has ->date, ->location_name,
                                ->building_name and ->lines)
                   'breakdown'  that scorecard's own totals (same shape as $rollup)
  ============================================================================
--}}

@php
  // Work out two simple yes/no flags here, so the HTML below stays easy to read.
  $hasFilter = ! empty($filters['location_id']) || ! empty($filters['building_id']);
  $hasData   = $rollup['count'] > 0;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Monthly KPI report · {{ $monthLabel }} · M Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/brand.css" rel="stylesheet">
  <link href="/assets/css/components.css" rel="stylesheet">
  <link href="/assets/css/extras.css" rel="stylesheet">
</head>
<body style="background:#e9ecf2;">

  {{-- TOOLBAR — shown on screen, hidden when printing. Lets the user re-filter and print. --}}
  <div class="print-toolbar">
    <div>
      <a href="{{ route('kpi.reports.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to reports
      </a>
    </div>

    <form method="GET" action="{{ route('kpi.reports.monthly') }}" class="d-flex gap-2 align-items-center">
      <input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm" style="width:150px;">

      <select name="location_id" class="form-control form-control-sm" style="width:160px;">
        <option value="">All locations</option>
        @foreach ($locations as $location)
          <option value="{{ $location->id }}" @selected(($filters['location_id'] ?? '') == $location->id)>
            {{ $location->name }}
          </option>
        @endforeach
      </select>

      <select name="building_id" class="form-control form-control-sm" style="width:160px;">
        <option value="">All buildings</option>
        @foreach ($buildings as $building)
          <option value="{{ $building->id }}" @selected(($filters['building_id'] ?? '') == $building->id)>
            {{ $building->name }}
          </option>
        @endforeach
      </select>

      <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-funnel me-1"></i>Apply</button>
    </form>

    <div class="d-flex gap-2">
      <button class="btn btn-primary btn-sm" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Print / Save PDF
      </button>
    </div>
  </div>

  {{-- THE PRINTABLE PAGE --}}
  <article class="print-page">

    {{-- Branded header bar (logo on the left, report meta on the right) --}}
    <header class="brand-bar">
      <div class="logo">
        <div class="mark">M</div>
        <div>
          <div style="font-weight:800;font-size:14px;line-height:1.1;">M Dashboard Corp.</div>
          <div style="font-size:11px;color:#555;">Security Operations · 12 Independence Ave., Accra</div>
        </div>
      </div>
      <div class="meta">
        <div style="font-weight:700;color:#000;font-size:11px;">Monthly KPI Report</div>
        <div>Period: {{ $monthLabel }}</div>
        <div>Generated: {{ now()->format('d M Y · H:i') }}</div>
        <div>Reference: KPI-RPT-{{ $month }}</div>
      </div>
    </header>

    <h1>Monthly KPI Performance Report</h1>
    <div style="color:#555;font-size:12px;">
      Prepared for the Head of Security · M Dashboard Corp. — Security Operations
      @if ($hasFilter)
        <br>Filtered scope applied.
      @endif
    </div>

    {{-- If the month has no scorecards, show a short notice and nothing else. --}}
    @if (! $hasData)

      <h2>No data</h2>
      <p>No scorecards were filed for {{ $monthLabel }} within the selected scope.</p>

    @else

      {{-- Pull the best/worst groups into plain variables so the markup is tidy. --}}
      @php
        $best  = $rollup['best'];
        $worst = $rollup['worst'];
      @endphp

      {{-- 1. Executive summary --}}
      <h2>1. Executive summary</h2>
      <p>
        For {{ $monthLabel }}, the security team filed
        <strong>{{ $rollup['count'] }}</strong> scorecard{{ $rollup['count'] === 1 ? '' : 's' }}.
        The composite KPI score was <strong>{{ $rollup['overall_pct'] }}%</strong>.
        Performance was strongest in {{ $best['name'] }} ({{ $best['attainment'] }}%)
        and weakest in {{ $worst['name'] }} ({{ $worst['attainment'] }}%).
      </p>

      <div class="summary-grid">
        <div class="cell"><div class="lbl">Overall KPI</div><div class="val">{{ $rollup['overall_pct'] }}%</div></div>
        <div class="cell"><div class="lbl">Scorecards</div><div class="val">{{ $rollup['count'] }}</div></div>
        <div class="cell"><div class="lbl">Best group</div><div class="val" style="font-size:13px;">{{ $best['name'] }} {{ $best['attainment'] }}%</div></div>
        <div class="cell"><div class="lbl">Worst group</div><div class="val" style="font-size:13px;">{{ $worst['name'] }} {{ $worst['attainment'] }}%</div></div>
      </div>

      {{-- 2. KPI group results (averaged across the whole month) --}}
      <h2>2. KPI group results</h2>
      <table class="print-table">
        <thead>
          <tr>
            <th style="width:6%">#</th>
            <th>KPI group</th>
            <th style="width:14%">Weight</th>
            <th style="width:18%">Avg attainment</th>
            <th style="width:18%">Weighted points</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($rollup['groups'] as $index => $group)
            @php
              // Weighted points = how many of this group's weight it actually earned.
              $weightedPoints = round($group['weight'] * $group['attainment'] / 100, 1);
            @endphp
            <tr>
              <td>{{ $index + 1 }}</td>
              <td><strong>{{ $group['name'] }}</strong></td>
              <td>{{ $group['weight'] }}%</td>
              <td>{{ $group['attainment'] }}%</td>
              <td>{{ $weightedPoints }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      {{-- 3. One row per scorecard, just the headline score --}}
      <h2>3. Scorecards in period</h2>
      <table class="print-table">
        <thead>
          <tr>
            <th style="width:14%">Date</th>
            <th>Location</th>
            <th>Building</th>
            <th style="width:14%">Overall %</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($reports as $report)
            @php $card = $report['card']; @endphp
            <tr>
              <td>{{ $card->date->format('d M Y') }}</td>
              <td>{{ $card->location_name }}</td>
              <td>{{ $card->building_name }}</td>
              <td><strong>{{ $report['breakdown']['overall_pct'] }}%</strong></td>
            </tr>
          @endforeach
        </tbody>
      </table>

      {{--
        4. Full scorecard detail — every line of every scorecard.
        This is the "print all the details" section. We start it on a fresh
        printed page, then show one table per scorecard.
      --}}
      <h2 style="page-break-before:always;">4. Full scorecard detail</h2>

      @foreach ($reports as $report)
        @php $card = $report['card']; @endphp

        {{-- Keep each scorecard's block together when printing. --}}
        <div style="page-break-inside:avoid;margin-bottom:18px;">

          <h3 style="font-size:14px;margin:14px 0 6px;">
            {{ $card->date->format('d M Y') }}
            · {{ $card->location_name }} / {{ $card->building_name }}
            — <strong>{{ $report['breakdown']['overall_pct'] }}%</strong>
          </h3>

          <table class="print-table">
            <thead>
              <tr>
                <th style="width:20%">Group</th>
                <th>Criteria</th>
                <th style="width:12%">Target</th>
                <th style="width:12%">Scored</th>
                <th style="width:12%">Merit</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($card->lines as $line)
                <tr>
                  <td>{{ $line->group?->name ?? '—' }}</td>
                  <td>{{ $line->criteria }}</td>
                  <td>{{ $line->target ?? '—' }}</td>
                  <td>{{ $line->scored ?? '—' }}</td>
                  <td>{{ $line->merit !== null ? number_format($line->merit, 1) : '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

        </div>
      @endforeach

    @endif

    {{-- Signature lines --}}
    <div class="sig-row">
      <div><div class="sig-line">Prepared by — Senior Officer</div></div>
      <div><div class="sig-line">Approved by — Head of Security</div></div>
    </div>

    <footer>
      <span>M Dashboard Corp. · CONFIDENTIAL</span>
      <span>KPI-RPT-{{ $month }}</span>
    </footer>
  </article>

</body>
</html>
