<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuildingController extends Controller
{
    public function index()
    {
        return view('locations', [
            'buildings' => Building::with('location')->orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'location_id' => ['required', 'exists:locations,id'],
        ]);

        Building::create($data);

        return redirect()->route('locations.index')->with('status', 'Building added.');
    }

    public function show(Building $building)
    {
        abort(404);
    }

    public function edit(Building $building)
    {
        abort(404);
    }

    public function update(Request $request, Building $building)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'location_id' => ['required', 'exists:locations,id'],
        ]);

        $building->update($data);

        return redirect()->route('locations.index')->with('status', 'Building updated.');
    }

    public function destroy(Building $building)
    {
        $building->delete();

        return redirect()->route('locations.index')->with('status', 'Building deleted.');
    }
}
