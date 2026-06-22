<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Deployment;
use App\Models\KpiGroup;
use App\Models\KpiScorecard;
use App\Models\KpiSubItem;
use App\Models\Location;
use App\Models\Place;
use App\Support\KpiScoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KpiEntryController extends Controller
{
    public function index()
    {
        return view('kpi.entry', [
            'groups'      => KpiGroup::with('subItems')->orderBy('id')->get(),
            'locations'   => Location::orderBy('name')->get(),
            'buildings'   => Building::orderBy('name')->get(['id', 'name', 'location_id']),
            'places'      => Place::orderBy('name')->get(['id', 'name', 'building_id', 'estimated_guards']),
            'deployments' => Deployment::with(['securityCompany:id,name', 'shift:id,name'])->get()
                ->map(fn ($d) => [
                    'building_id' => $d->building_id,
                    'date'        => $d->start_at->toDateString(),
                    'company'     => $d->securityCompany?->name,
                    'shift'       => $d->shift?->name,
                    'guards'      => $d->number_of_guards,
                    'officer'     => $d->supervising_officer,
                    'start'       => $d->start_at->format('H:i'),
                    'end'         => $d->end_at->format('H:i'),
                ])->values(),
        ]);
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'location_id'   => ['required', 'exists:locations,id'],
            'building_id'   => ['required', 'exists:buildings,id'],
            'date'          => [
                'required',
                'date',
                Rule::unique('kpi_scorecards')
                    ->where('location_id', $request->location_id)
                    ->where('building_id', $request->building_id),
            ],
            'comments'      => ['nullable', 'string'],
            'scored'        => ['array'],
            'scored.*'      => ['nullable', 'integer', 'min:0'],
            'beat_scored'   => ['array'],
            'beat_scored.*' => ['nullable', 'integer', 'min:0'],
        ], [
            'date.unique' => 'A scorecard already exists for this location and building on this date.',
        ]);

        DB::transaction(function () use ($request, $data) {
            $scorecard = KpiScorecard::create([
                'location_id'   => $data['location_id'],
                'building_id'   => $data['building_id'],
                'location_name' => Location::find($data['location_id'])->name,
                'building_name' => Building::find($data['building_id'])->name,
                'date'          => $data['date'],
                'comments'      => $data['comments'] ?? null,
            ]);

            // Standard sub-item lines — snapshot criteria + target from config.
            foreach ($data['scored'] ?? [] as $subItemId => $scored) {
                $subItem = KpiSubItem::find($subItemId);
                if (! $subItem) {
                    continue;
                }

                $scorecard->lines()->create([
                    'kpi_group_id'    => $subItem->kpi_group_id,
                    'kpi_sub_item_id' => $subItem->id,
                    'criteria'        => $subItem->criteria,
                    'target'          => $subItem->target,
                    'scored'          => $scored,
                    // merit is system-generated below via KpiScoring::recompute.
                ]);
            }

            // Deployment beats — one line per place, snapshot name + estimated guards.
            $deploymentGroupId = KpiGroup::where('name', 'Deployment')->value('id');

            foreach ($data['beat_scored'] ?? [] as $placeId => $scored) {
                $place = Place::find($placeId);
                if (! $place) {
                    continue;
                }

                $scorecard->lines()->create([
                    'kpi_group_id' => $deploymentGroupId,
                    'place_id'     => $place->id,
                    'criteria'     => $place->name,
                    'target'       => $place->estimated_guards,
                    'scored'       => $scored,
                    // merit is system-generated below via KpiScoring::recompute.
                ]);
            }

            KpiScoring::recompute($scorecard);
        });

        return redirect()->route('entries.index')
            ->with('status', 'Scorecard submitted.');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(Request $request, string $id)
    {
        abort(404);
    }

    public function destroy(string $id)
    {
        abort(404);
    }
}
