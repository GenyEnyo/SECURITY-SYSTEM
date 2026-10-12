<?php

namespace App\Http\Controllers;

use App\Models\KpiGroup;
use App\Models\Location;
use Closure;
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
            'weight' => ['required', 'integer', 'min:0', 'max:100', $this->withinTotalWeight()],
        ]);

        KpiGroup::create($data);

        return redirect()->route('kpi.settings')
            ->with('status', 'KPI group added.');
    }

    public function update(Request $request, KpiGroup $kpiGroup)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:0', 'max:100', $this->withinTotalWeight($kpiGroup)],
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

    /** All group weights together may not exceed 100. */
    private function withinTotalWeight(?KpiGroup $editing = null): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($editing) {
            $used = (int) KpiGroup::when($editing, fn ($q) => $q->whereKeyNot($editing->id))->sum('weight');

            if ($used + (int) $value > 100) {
                $left = max(100 - $used, 0);
                $fail("Total KPI weight cannot exceed 100. The other groups already use {$used}, so this group can be at most {$left}.");
            }
        };
    }
}
