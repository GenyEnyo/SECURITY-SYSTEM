<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Deployment;
use App\Models\Location;
use App\Models\Place;
use App\Models\SecurityCompany;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeploymentTest extends TestCase
{
    use RefreshDatabase;

    private Building $building;

    private SecurityCompany $company;

    private User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $accra = Location::where('name', 'Accra')->firstOrFail();
        $this->building = Building::where('name', 'Head Office')->firstOrFail();
        Place::create(['building_id' => $this->building->id, 'name' => 'Main gate', 'estimated_guards' => 4]);
        Place::create(['building_id' => $this->building->id, 'name' => 'Car park', 'estimated_guards' => 6]);

        $this->company = SecurityCompany::where('name', 'G4S Ghana')->firstOrFail();
        $this->company->update(['location_id' => $accra->id]);

        $this->supervisor = User::factory()->create([
            'location_id' => $accra->id,
            'department'  => 'Real Estate and Security',
            'section'     => 'Security',
        ])->assignRole('Supervisor');
    }

    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'security_company_id' => $this->company->id,
            'supervisor_id'       => $this->supervisor->id,
            'number_of_guards'    => 9,
            'start_at'            => '2026-10-06 06:00',
            'end_at'              => '2026-10-07 06:00',
        ];
    }

    public function test_add_page_prefills_expected_guards_company_and_supervisor(): void
    {
        $this->actingAs($this->supervisor)->get("/buildings/{$this->building->id}/deployments/create")
            ->assertOk()
            ->assertSee('id="f-expected" class="form-control" value="10"', false)
            ->assertSee('About 5 by day and 5 by night')
            ->assertSee('<option value="'.$this->company->id.'" selected>', false)
            ->assertSee('<option value="'.$this->supervisor->id.'" selected>', false)
            ->assertDontSee('Shift');
    }

    public function test_deployment_is_saved_as_full_day_with_expected_guards(): void
    {
        $this->actingAs($this->supervisor)
            ->post("/buildings/{$this->building->id}/deployments", $this->payload())
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $deployment = Deployment::firstOrFail();
        $this->assertSame(10, $deployment->expected_guards);
        $this->assertSame($this->supervisor->id, $deployment->supervisor_id);
        $this->assertSame($this->supervisor->name, $deployment->supervising_officer);
        $this->assertSame('Full Day', $deployment->shift->name);

        $this->actingAs($this->supervisor)->get("/buildings/{$this->building->id}/deployments")
            ->assertOk()
            ->assertSee('Expected Guards')
            ->assertSee('G4S Ghana');
    }

    public function test_supervisor_must_be_a_security_officer_at_the_location(): void
    {
        $outsider = User::factory()->create(['location_id' => $this->supervisor->location_id, 'department' => 'Finance']);

        $this->actingAs($this->supervisor)
            ->post("/buildings/{$this->building->id}/deployments", $this->payload(['supervisor_id' => $outsider->id]))
            ->assertSessionHasErrors('supervisor_id');
    }

    public function test_deployment_cannot_be_opened_through_another_building(): void
    {
        $this->actingAs($this->supervisor)->post("/buildings/{$this->building->id}/deployments", $this->payload());
        $deployment = Deployment::firstOrFail();
        $other = Building::where('name', 'VRA Hospital')->firstOrFail();

        $this->actingAs($this->supervisor)->get("/buildings/{$other->id}/deployments/{$deployment->id}")->assertNotFound();
    }
}
