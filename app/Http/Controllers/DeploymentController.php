<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Deployment;
use App\Models\SecurityCompany;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $deployments = $building->deployments()->with('securityCompany')->get();
        $expectedGuards = $this->expectedGuards($building);

        return view('buildings.deployments.index', compact('building', 'deployments', 'expectedGuards'));
    }

    public function create(Building $building)
    {
        $companies = $this->companies();

        return view('buildings.deployments.create', [
            'building'         => $building->load('location'),
            'companies'        => $companies,
            'defaultCompanyId' => $companies->firstWhere('location_id', $building->location_id)?->id,
            'officers'         => $this->officers($building),
            'expectedGuards'   => $this->expectedGuards($building),
        ]);
    }

    public function store(Request $request, Building $building)
    {
        $data = $this->validatedData($request, $building);
        $data['expected_guards'] = $this->expectedGuards($building);

        $building->deployments()->create($data);

        return redirect()->route('buildings.deployments.index', $building)
            ->with('status', 'Deployment added.');
    }

    public function show(Building $building, Deployment $deployment)
    {
        $building->load('location');
        $deployment->load('securityCompany');

        return view('buildings.deployments.show', compact('building', 'deployment'));
    }

    public function edit(Building $building, Deployment $deployment)
    {
        return view('buildings.deployments.edit', [
            'building'       => $building->load('location'),
            'deployment'     => $deployment,
            'companies'      => $this->companies($deployment),
            'officers'       => $this->officers($building, $deployment),
            'expectedGuards' => $deployment->expected_guards ?? $this->expectedGuards($building),
        ]);
    }

    public function update(Request $request, Building $building, Deployment $deployment)
    {
        $data = $this->validatedData($request, $building, $deployment);
        $data['expected_guards'] = $deployment->expected_guards ?? $this->expectedGuards($building);

        $deployment->update($data);

        return redirect()->route('buildings.deployments.show', [$building, $deployment])
            ->with('status', 'Deployment updated.');
    }

    public function destroy(Building $building, Deployment $deployment)
    {
        $deployment->delete();

        return redirect()->route('buildings.deployments.index', $building)
            ->with('status', 'Deployment deleted.');
    }

    /** Estimated guards per day for the whole building: the sum of its places' estimates (set in KPI settings). */
    private function expectedGuards(Building $building): int
    {
        return (int) $building->places()->sum('estimated_guards');
    }

    /** Companies the user may pick, plus the one already on the deployment being edited. */
    private function companies(?Deployment $deployment = null): Collection
    {
        $companies = SecurityCompany::orderBy('name')->get();

        if ($deployment?->securityCompany && ! $companies->contains('id', $deployment->security_company_id)) {
            $companies->push($deployment->securityCompany);
        }

        return $companies;
    }

    /** Security-section users at the building's location, plus the logged-in user and the current supervisor. */
    private function officers(Building $building, ?Deployment $deployment = null): Collection
    {
        return User::supervisingOfficers($building->location_id)
            ->orWhereIn('id', array_filter([auth()->id(), $deployment?->supervisor_id]))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function validatedData(Request $request, Building $building, ?Deployment $deployment = null): array
    {
        $officers = $this->officers($building, $deployment);

        $data = $request->validate([
            'security_company_id' => ['required', Rule::in($this->companies($deployment)->pluck('id'))],
            'supervisor_id'       => ['required', Rule::in($officers->pluck('id'))],
            'number_of_guards'    => ['required', 'integer', 'min:1'],
            'start_at'            => ['required', 'date'],
            'end_at'              => ['required', 'date', 'after:start_at'],
            'notes'               => ['nullable', 'string'],
        ]);

        // Deployments cover the full day; the shift row is kept for reports that group by shift.
        $data['shift_id'] = Shift::firstOrCreate(['name' => Deployment::FULL_DAY_SHIFT])->id;
        $data['supervising_officer'] = $officers->firstWhere('id', (int) $data['supervisor_id'])->name;

        return $data;
    }
}
