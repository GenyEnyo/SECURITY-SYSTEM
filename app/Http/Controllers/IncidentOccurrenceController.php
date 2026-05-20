<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentOccurrenceRequest;
use App\Http\Requests\UpdateIncidentOccurrenceRequest;
use App\Models\IncidentOccurrence;
use App\Models\IncidentStatus;
use App\Models\IncidentType;
use App\Models\Location;
use App\Models\Severity;
use Illuminate\Support\Facades\Storage;

class IncidentOccurrenceController extends Controller
{
    public function index()
    {
        $occurrences = IncidentOccurrence::with(['incidentType', 'location', 'severity', 'status', 'user'])
            ->latest('occurred_at')
            ->paginate(15);

        return view('incidents.index', compact('occurrences'));
    }

    public function all()
    {
        $occurrences = IncidentOccurrence::with(['incidentType', 'location', 'severity', 'status', 'user'])
            ->latest('occurred_at')
            ->paginate(15);

        return view('incidents.all', compact('occurrences'));
    }

    public function create()
    {
        return view('incidents.create', $this->lookups());
    }

    public function store(StoreIncidentOccurrenceRequest $request)
    {
        $data = $request->validated();
        $data['incident_status_id'] ??= IncidentStatus::where('name', 'Reported')->value('id');
        $data['user_id'] = 1;

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('incidents', 'public');
        }

        IncidentOccurrence::create($data);

        return redirect()->route('incidents.index')->with('status', 'Incident saved');
    }

    public function show(IncidentOccurrence $incident)
    {
        $incident->load(['incidentType', 'location', 'severity', 'status', 'user']);

        return view('incidents.show', ['incident' => $incident]);
    }

    public function edit(IncidentOccurrence $incident)
    {
        return view('incidents.edit', array_merge(
            ['incident' => $incident],
            $this->lookups(),
        ));
    }

    public function update(UpdateIncidentOccurrenceRequest $request, IncidentOccurrence $incident)
    {
        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            if ($incident->attachment_path) {
                Storage::disk('public')->delete($incident->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('incidents', 'public');
        }

        $incident->update($data);

        return redirect()->route('incidents.index')->with('status', 'Incident updated');
    }

    public function destroy(IncidentOccurrence $incident)
    {
        if ($incident->attachment_path) {
            Storage::disk('public')->delete($incident->attachment_path);
        }
        $incident->delete();

        return redirect()->route('incidents.index')->with('status', 'Incident deleted');
    }

    private function lookups(): array
    {
        return [
            'incidentTypes' => IncidentType::orderBy('name')->get(),
            'locations'     => Location::orderBy('name')->get(),
            'severities'    => Severity::orderBy('id')->get(),
            'statuses'      => IncidentStatus::orderBy('id')->get(),
        ];
    }
}
