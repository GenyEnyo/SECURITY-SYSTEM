<?php

namespace App\Http\Controllers;

use App\Models\IncidentStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncidentStatusController extends Controller
{
    public function index()
    {
        $statuses = IncidentStatus::orderBy('id')->get();

        return view('settings.incident-statuses', ['statuses' => $statuses]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:incident_statuses,name'],
        ]);

        IncidentStatus::create($data);

        return redirect()->route('settings.incident-statuses.index')
            ->with('status', 'Incident status added.');
    }

    public function update(Request $request, IncidentStatus $incidentStatus)
    {
        if ($incidentStatus->isProtected()) {
            return redirect()->route('settings.incident-statuses.index')
                ->with('error', "\"{$incidentStatus->name}\" is used by the system and cannot be changed.");
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('incident_statuses', 'name')->ignore($incidentStatus->id)],
        ]);

        $incidentStatus->update($data);

        return redirect()->route('settings.incident-statuses.index')
            ->with('status', 'Incident status updated.');
    }

    public function destroy(IncidentStatus $incidentStatus)
    {
        if ($incidentStatus->isProtected()) {
            return redirect()->route('settings.incident-statuses.index')
                ->with('error', "\"{$incidentStatus->name}\" is used by the system and cannot be deleted.");
        }

        if ($incidentStatus->occurrences()->withTrashed()->exists()) {
            return redirect()->route('settings.incident-statuses.index')
                ->with('error', "\"{$incidentStatus->name}\" is in use on incidents and cannot be deleted.");
        }

        $incidentStatus->delete();

        return redirect()->route('settings.incident-statuses.index')
            ->with('status', 'Incident status deleted.');
    }
}
