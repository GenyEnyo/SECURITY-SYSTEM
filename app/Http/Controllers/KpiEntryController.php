<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Deployment;
use App\Models\KpiGroup;
use App\Models\Location;
use App\Models\Place;
use Illuminate\Http\Request;

class KpiEntryController extends Controller
{
    public function index()
    {
        return view('kpi.entry', [
            'groups'      => KpiGroup::with('subItems')->orderBy('id')->get(),
            'locations'   => Location::orderBy('name')->get(),
            'buildings'   => Building::orderBy('name')->get(['id', 'name', 'location_id']),
            'places'      => Place::orderBy('name')->get(['id', 'name', 'building_id']),
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
        abort(404);
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
