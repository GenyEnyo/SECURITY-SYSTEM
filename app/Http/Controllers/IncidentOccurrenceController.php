<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentOccurrenceRequest;
use App\Http\Requests\UpdateIncidentOccurrenceRequest;
use App\Models\Building;
use App\Models\IncidentOccurrence;
use App\Models\IncidentStatus;
use App\Models\IncidentType;
use App\Models\Location;
use App\Models\Place;
use App\Models\Severity;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class IncidentOccurrenceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Report Incident', only: ['create', 'store']),
            new Middleware('permission:View Incidents', only: ['index', 'show']),
            new Middleware('permission:View All Incidents', only: ['all']),
            new Middleware('permission:Edit Incident', only: ['edit', 'update']),
            new Middleware('permission:Delete Incident', only: ['destroy']),
            new Middleware('permission:Acknowledge Incident', only: ['acknowledge']),
        ];
    }

    public function index()
    {
        $occurrences = IncidentOccurrence::with(['incidentType', 'location', 'building', 'place', 'severity', 'status', 'user'])
            ->unless(auth()->user()->can('View All Incidents'), fn ($query) => $query->where('user_id', auth()->id()))
            ->latest('occurred_at')
            ->get();

        return view('incidents.index', compact('occurrences'));
    }

    public function all()
    {
        $occurrences = IncidentOccurrence::with(['incidentType', 'location', 'building', 'place', 'severity', 'status', 'user'])
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
        $data['incident_status_id'] = IncidentStatus::where('name', 'Reported')->value('id');
        $data['user_id'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('incidents', 'public');
        }

        IncidentOccurrence::create($data);

        return redirect()->route('incidents.index')->with('status', 'Incident saved');
    }

    public function show(IncidentOccurrence $incident)
    {
        $this->authorizeAccess($incident);

        $incident->load(['incidentType', 'location', 'building', 'place', 'severity', 'status', 'user']);

        activity('incident_occurrence')
            ->causedBy(auth()->user())
            ->performedOn($incident)
            ->log('viewed');

        return view('incidents.show', ['incident' => $incident]);
    }

    public function edit(IncidentOccurrence $incident)
    {
        $this->authorizeAccess($incident);
        abort_if($incident->isLocked(), 403, 'This incident can no longer be changed.');

        return view('incidents.edit', array_merge(
            ['incident' => $incident],
            $this->lookups(),
        ));
    }

    public function update(UpdateIncidentOccurrenceRequest $request, IncidentOccurrence $incident)
    {
        $this->authorizeAccess($incident);
        abort_if($incident->isLocked(), 403, 'This incident can no longer be changed.');

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
        $this->authorizeAccess($incident);
        abort_if($incident->isLocked(), 403, 'This incident can no longer be changed.');

        if ($incident->attachment_path) {
            Storage::disk('public')->delete($incident->attachment_path);
        }
        $incident->delete();

        return redirect()->route('incidents.index')->with('status', 'Incident deleted');
    }

    public function acknowledge(IncidentOccurrence $incident)
    {
        if (! $incident->isAcknowledged()) {
            $incident->update([
                'acknowledged_at'    => now(),
                'incident_status_id' => IncidentStatus::where('name', 'Reviewing')->value('id'),
            ]);
        }

        return back()->with('status', 'Incident acknowledged.');
    }

    // Users without "View All Incidents" may only work with incidents they reported.
    private function authorizeAccess(IncidentOccurrence $incident): void
    {
        abort_unless(
            $incident->user_id === auth()->id() || auth()->user()->can('View All Incidents'),
            403,
            'You can only access incidents you reported.',
        );
    }

    private function lookups(): array
    {
        return [
            'incidentTypes' => IncidentType::orderBy('name')->get(),
            'locations'     => Location::orderBy('name')->get(),
            'buildings'     => Building::orderBy('name')->get(['id', 'name', 'location_id']),
            'places'        => Place::orderBy('name')->get(['id', 'name', 'building_id']),
            'severities'    => Severity::orderBy('id')->get(),
            'statuses'      => IncidentStatus::orderBy('id')->get(),
        ];
    }
}
