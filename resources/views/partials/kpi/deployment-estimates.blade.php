<p class="muted fw-5 mb-3" style="font-size:13px;">
  Set the estimated number of guards for each place. Expand a location, then a building, to fill in its places.
</p>

@forelse ($locations as $location)
  @php
    $locPlaces = $location->buildings->flatMap->places;
    $locTotal  = $locPlaces->count();
    $locSet    = $locPlaces->whereNotNull('estimated_guards')->count();
  @endphp
  <section class="kpi-group collapsed" data-location style="margin-bottom:12px;">
    <header>
      <div>
        <h4>{{ $location->name }}</h4>
        <div class="muted fw-5" style="font-size:12px;">
          {{ $location->buildings->count() }} building{{ $location->buildings->count() === 1 ? '' : 's' }}
        </div>
      </div>
      <div class="kpi-meta">
        <span>{{ $locSet }} / {{ $locTotal }} places set</span>
        <i class="bi bi-chevron-down chev" style="font-size:16px;"></i>
      </div>
    </header>
    <div class="body">
      @forelse ($location->buildings as $building)
        @php
          $placeCount = $building->places->count();
          $setCount   = $building->places->whereNotNull('estimated_guards')->count();
        @endphp
        <section class="kpi-group collapsed" data-building style="margin-bottom:12px;">
          <header>
            <div>
              <h4>{{ $building->name }}</h4>
              <div class="muted fw-5" style="font-size:12px;">
                {{ $placeCount }} place{{ $placeCount === 1 ? '' : 's' }}
              </div>
            </div>
            <div class="kpi-meta">
              <span>{{ $setCount }} / {{ $placeCount }} set</span>
              <i class="bi bi-chevron-down chev" style="font-size:16px;"></i>
            </div>
          </header>
          <div class="body">
            @if ($placeCount === 0)
              <p class="muted fw-5 mb-0" style="font-size:13px;">No places in this building yet.</p>
            @else
              <form method="POST" action="{{ route('buildings.place-estimates.update', $building) }}">
                @csrf
                @method('PUT')
                <table class="kpi-table">
                  <thead>
                    <tr>
                      <th>Place</th>
                      <th style="width:160px">Estimated guards</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($building->places as $place)
                      <tr>
                        <td>{{ $place->name }}</td>
                        <td>
                          <input type="number" name="estimates[{{ $place->id }}]"
                                 min="0" value="{{ $place->estimated_guards }}">
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                <button type="submit" class="btn btn-success btn-sm mt-3">
                  <i class="bi bi-check-lg me-2"></i>Save estimates
                </button>
              </form>
            @endif
          </div>
        </section>
      @empty
        <div class="muted fw-5" style="padding:32px;text-align:center;border:1px dashed var(--border-soft);border-radius:12px;">
          No buildings under this location yet.
        </div>
      @endforelse
    </div>
  </section>
@empty
  <div class="muted fw-5" style="padding:32px;text-align:center;border:1px dashed var(--border-soft);border-radius:12px;">
    No locations yet.
  </div>
@endforelse
