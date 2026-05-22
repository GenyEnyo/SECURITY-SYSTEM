<?php

namespace App\Http\Controllers;

use App\Models\IncidentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncidentTypeController extends Controller
{
    public function index()
    {
        $types = IncidentType::orderBy('name')->get();

        return view('settings.incident-types', ['types' => $types]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:incident_types,name'],
        ]);

        IncidentType::create($data);

        return redirect()->route('settings.incident-types.index')
            ->with('status', 'Incident type added.');
    }

    public function update(Request $request, IncidentType $incidentType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('incident_types', 'name')->ignore($incidentType->id)],
        ]);

        $incidentType->update($data);

        return redirect()->route('settings.incident-types.index')
            ->with('status', 'Incident type updated.');
    }

    public function destroy(IncidentType $incidentType)
    {
        $incidentType->delete();

        return redirect()->route('settings.incident-types.index')
            ->with('status', 'Incident type deleted.');
    }
}
