<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:locations,name'],
        ]);

        Location::create($data);

        return redirect()->route('locations.index')
            ->with('status', 'Location added.');
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('locations', 'name')->ignore($location->id)],
        ]);

        $location->update($data);

        return redirect()->route('locations.index')
            ->with('status', 'Location updated.');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('locations.index')
            ->with('status', 'Location deleted.');
    }
}
