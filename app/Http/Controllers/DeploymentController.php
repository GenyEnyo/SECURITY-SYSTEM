<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Deployment;
use App\Models\SecurityCompany;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeploymentController extends Controller
{
    public function picker()
    {
        return view('deployments.index', [
            'buildings' => Building::with('location')->orderBy('name')->get(),
        ]);
    }

    public function index(Building $building)
    {
        $building->load('location');
        $deployments = $building->deployments()->with('shift')->get();

        return view('buildings.deployments.index', compact('building', 'deployments'));
    }

    public function create(Building $building)
    {
        return view('buildings.deployments.create', [
            'building'  => $building->load('location'),
            'companies' => SecurityCompany::orderBy('name')->get(),
            'shifts'    => Shift::orderBy('id')->get(),
            'officers'  => Deployment::SUPERVISING_OFFICERS,
        ]);
    }

    public function store(Request $request, Building $building)
    {
        $building->deployments()->create($this->validatedData($request));

        return redirect()->route('buildings.deployments.index', $building)
            ->with('status', 'Deployment added.');
    }

    public function show(Building $building, Deployment $deployment)
    {
        $building->load('location');
        $deployment->load(['shift', 'securityCompany']);

        return view('buildings.deployments.show', compact('building', 'deployment'));
    }

    public function edit(Building $building, Deployment $deployment)
    {
        return view('buildings.deployments.edit', [
            'building'   => $building->load('location'),
            'deployment' => $deployment,
            'companies'  => SecurityCompany::orderBy('name')->get(),
            'shifts'     => Shift::orderBy('id')->get(),
            'officers'   => Deployment::SUPERVISING_OFFICERS,
        ]);
    }

    public function update(Request $request, Building $building, Deployment $deployment)
    {
        $deployment->update($this->validatedData($request));

        return redirect()->route('buildings.deployments.show', [$building, $deployment])
            ->with('status', 'Deployment updated.');
    }

    public function destroy(Building $building, Deployment $deployment)
    {
        $deployment->delete();

        return redirect()->route('buildings.deployments.index', $building)
            ->with('status', 'Deployment deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'security_company_id'  => ['required', 'exists:security_companies,id'],
            'shift_id'             => ['required', 'exists:shifts,id'],
            'supervising_officer'  => ['required', 'string', Rule::in(Deployment::SUPERVISING_OFFICERS)],
            'number_of_guards'     => ['required', 'integer', 'min:1'],
            'start_at'             => ['required', 'date'],
            'end_at'               => ['required', 'date', 'after:start_at'],
            'notes'                => ['nullable', 'string'],
        ]);
    }
}
