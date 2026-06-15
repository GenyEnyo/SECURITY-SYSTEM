<?php

namespace App\Http\Controllers;

use App\Models\KpiGroup;
use App\Models\Location;
use Illuminate\Http\Request;

class KpiGroupController extends Controller
{
    public function index()
    {
        $groups = KpiGroup::with('subItems')->orderBy('id')->get();

        $locations = Location::with([
            'buildings'        => fn ($q) => $q->orderBy('name'),
            'buildings.places' => fn ($q) => $q->orderBy('name'),
        ])->orderBy('name')->get();

        return view('kpi.settings', compact('groups', 'locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        KpiGroup::create($data);

        return redirect()->route('kpi.settings')
            ->with('status', 'KPI group added.');
    }

    public function update(Request $request, KpiGroup $kpiGroup)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $kpiGroup->update($data);

        return redirect()->route('kpi.settings')
            ->with('status', 'KPI group updated.');
    }

    public function destroy(KpiGroup $kpiGroup)
    {
        $kpiGroup->subItems()->delete();
        $kpiGroup->delete();

        return redirect()->route('kpi.settings')
            ->with('status', 'KPI group deleted.');
    }
}
