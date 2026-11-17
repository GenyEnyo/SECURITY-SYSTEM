<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Lets an admin correct a staff member's location when HR's is wrong. The
 * correction is used instead of HR's location until HR changes it.
 */
class StaffLocationController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::withoutGlobalScope('user_location')->orderBy('name')->get(['id', 'name']);
        $knownNames = $locations->map(fn ($l) => strtolower($l->name))->all();

        $staff = Staff::query()
            ->with('location')
            ->when($request->filled('search'), function (Builder $q) use ($request) {
                $term = '%'.strtolower(trim($request->search)).'%';
                $q->where(fn (Builder $w) => $w
                    ->whereRaw('LOWER(full_name) LIKE ?', [$term])
                    ->orWhere('staff_number', 'like', $term));
            })
            ->when($request->filled('location_id'), function (Builder $q) use ($request, $locations) {
                $name = strtolower((string) $locations->firstWhere('id', (int) $request->location_id)?->name);
                $q->where(fn (Builder $w) => $w
                    ->where('location_id', $request->location_id)
                    ->orWhere(fn (Builder $hr) => $hr->whereNull('location_id')->whereRaw('LOWER(location_name) = ?', [$name])));
            })
            ->when($request->filter === 'unrecognised', fn (Builder $q) => $q
                ->whereNull('location_id')
                ->where(fn (Builder $w) => $w
                    ->whereNull('location_name')
                    ->orWhereRaw('LOWER(location_name) NOT IN ('.implode(',', array_fill(0, count($knownNames), '?')).')', $knownNames)))
            ->when($request->filter === 'corrected', fn (Builder $q) => $q->whereNotNull('location_id'))
            ->orderBy('full_name')
            ->paginate(25)
            ->withQueryString();

        return view('settings.staff-locations', [
            'staff'      => $staff,
            'locations'  => $locations,
            'knownNames' => $knownNames,
            'filters'    => $request->only(['search', 'location_id', 'filter']),
        ]);
    }

    public function update(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
        ]);

        if (filled($data['location_id'] ?? null)) {
            $staff->update([
                'location_id'                  => $data['location_id'],
                // Remember HR's value now; when HR changes it, the correction is dropped.
                'location_hr_name_at_override' => $staff->location_name,
            ]);
            $message = "{$staff->full_name} now set to {$staff->fresh()->location->name}.";
        } else {
            $staff->update(['location_id' => null, 'location_hr_name_at_override' => null]);
            $message = "{$staff->full_name} now uses the HR location".($staff->location_name ? " ({$staff->location_name})." : '.');
        }

        return back()->with('status', $message);
    }
}
