<?php

namespace App\Http\Controllers;

use App\Models\KpiGroup;
use App\Models\KpiSubItem;
use Illuminate\Http\Request;

class KpiSubItemController extends Controller
{
    public function store(Request $request, KpiGroup $kpiGroup)
    {
        $data = $request->validate([
            'criteria' => ['required', 'string', 'max:255'],
            'target'   => ['required', 'integer', 'min:0'],
        ]);

        $kpiGroup->subItems()->create($data);

        return redirect()->route('kpi.settings')
            ->with('status', 'Sub-item added.');
    }

    public function update(Request $request, KpiSubItem $kpiSubItem)
    {
        $data = $request->validate([
            'criteria' => ['required', 'string', 'max:255'],
            'target'   => ['required', 'integer', 'min:0'],
        ]);

        $kpiSubItem->update($data);

        return redirect()->route('kpi.settings')
            ->with('status', 'Sub-item updated.');
    }

    public function destroy(KpiSubItem $kpiSubItem)
    {
        $kpiSubItem->delete();

        return redirect()->route('kpi.settings')
            ->with('status', 'Sub-item deleted.');
    }
}
