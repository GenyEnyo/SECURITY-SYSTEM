<p class="text-muted mb-3">
  Set the estimated number of guards for each place. Expand a location to fill in its buildings' places.
</p>

<div class="accordion" id="estLocations">
  @forelse ($locations as $location)
    @php
      $locPlaces = $location->buildings->flatMap->places;
      $locTotal  = $locPlaces->count();
      $locSet    = $locPlaces->whereNotNull('estimated_guards')->count();
    @endphp
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#estLoc{{ $location->id }}" aria-expanded="false" aria-controls="estLoc{{ $location->id }}">
          <span class="fw-semibold">{{ $location->name }}</span>
          <span class="text-muted ms-2 small">
            {{ $location->buildings->count() }} building{{ $location->buildings->count() === 1 ? '' : 's' }}
          </span>
          <span class="badge bg-light text-dark ms-auto me-2">{{ $locSet }} / {{ $locTotal }} places set</span>
        </button>
      </h2>
      <div id="estLoc{{ $location->id }}" class="accordion-collapse collapse" data-bs-parent="#estLocations">
        <div class="accordion-body">
          @forelse ($location->buildings as $building)
            @php
              $placeCount = $building->places->count();
              $setCount   = $building->places->whereNotNull('estimated_guards')->count();
            @endphp
            <div class="card mb-2">
              <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">{{ $building->name }}</span>
                <span class="badge bg-light text-dark">{{ $setCount }} / {{ $placeCount }} set</span>
              </div>
              <div class="card-body">
                @if ($placeCount === 0)
                  <p class="text-muted mb-0">No places in this building yet.</p>
                @else
                  <form method="POST" action="{{ route('buildings.place-estimates.update', $building) }}">
                    @csrf
                    @method('PUT')
                    <div class="table-responsive">
                      <table class="table table-sm table-centered mb-0">
                        <thead class="table-light">
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
                                       class="form-control form-control-sm"
                                       min="0" value="{{ $place->estimated_guards }}">
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mt-3">Save estimates</button>
                  </form>
                @endif
              </div>
            </div>
          @empty
            <div class="text-muted text-center p-4 rounded" style="border:1px dashed var(--bs-border-color);">
              No buildings under this location yet.
            </div>
          @endforelse
        </div>
      </div>
    </div>
  @empty
    <div class="text-muted text-center p-4 rounded" style="border:1px dashed var(--bs-border-color);">
      No locations yet.
    </div>
  @endforelse
</div>
