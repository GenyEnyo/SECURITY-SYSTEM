<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlaceController extends Controller
{
    public function index(Building $building)
    {
        $building->load('location');
        $places = $building->places()->get();

        return view('buildings.places.index', compact('building', 'places'));
    }

    public function store(Request $request, Building $building)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('places', 'name')->where('building_id', $building->id)],
        ]);

        $building->places()->create($data);

        return redirect()->route('buildings.places.index', $building)
            ->with('status', 'Specific location added.');
    }

    public function update(Request $request, Building $building, Place $place)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('places', 'name')->where('building_id', $building->id)->ignore($place->id)],
        ]);

        $place->update($data);

        return redirect()->route('buildings.places.index', $building)
            ->with('status', 'Specific location updated.');
    }

    public function destroy(Building $building, Place $place)
    {
        $place->delete();

        return redirect()->route('buildings.places.index', $building)
            ->with('status', 'Specific location deleted.');
    }
}
