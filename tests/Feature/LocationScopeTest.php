<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\IncidentType;
use App\Models\Location;
use App\Models\Place;
use App\Models\SecurityCompany;
use App\Models\Severity;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationScopeTest extends TestCase
{
    use RefreshDatabase;

    private Location $accra;

    private Location $tema;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->accra = Location::where('name', 'Accra')->firstOrFail();
        $this->tema = Location::where('name', 'Tema')->firstOrFail();
    }

    private function supervisorAt(?Location $location): User
    {
        return User::factory()->create(['location_id' => $location?->id])->assignRole('Supervisor');
    }

    public function test_user_only_sees_buildings_in_their_location(): void
    {
        $this->actingAs($this->supervisorAt($this->accra))->get('/deployments')
            ->assertOk()
            ->assertSee('Head Office')
            ->assertDontSee('KTS');
    }

    public function test_user_cannot_open_another_locations_records_by_url(): void
    {
        $temaBuilding = Building::where('location_id', $this->tema->id)->firstOrFail();
        $accraBuilding = Building::where('location_id', $this->accra->id)->firstOrFail();
        $user = $this->supervisorAt($this->accra);

        $this->actingAs($user)->get("/buildings/{$temaBuilding->id}/deployments")->assertNotFound();
        $this->actingAs($user)->get("/buildings/{$accraBuilding->id}/deployments")->assertOk();
    }

    public function test_user_without_a_location_sees_nothing(): void
    {
        $this->actingAs($this->supervisorAt(null))->get('/deployments')
            ->assertOk()
            ->assertDontSee('Head Office')
            ->assertDontSee('KTS');
    }

    public function test_super_admin_sees_every_location(): void
    {
        $admin = User::where('email', 'kwasi@example.com')->firstOrFail();

        $this->actingAs($admin)->get('/deployments')
            ->assertOk()
            ->assertSee('Head Office')
            ->assertSee('KTS');
    }

    public function test_security_companies_are_limited_to_the_users_location(): void
    {
        SecurityCompany::where('name', 'G4S Ghana')->update(['location_id' => $this->accra->id]);
        SecurityCompany::where('name', 'Royal Knights Security')->update(['location_id' => $this->tema->id]);

        $this->actingAs($this->supervisorAt($this->accra))->get('/security-companies')
            ->assertOk()
            ->assertSee('G4S Ghana')
            ->assertDontSee('Royal Knights Security');
    }

    public function test_incident_cannot_be_reported_for_another_location(): void
    {
        $temaBuilding = Building::where('location_id', $this->tema->id)->firstOrFail();
        $place = Place::create(['building_id' => $temaBuilding->id, 'name' => 'Main gate']);
        $officer = User::factory()->create(['location_id' => $this->accra->id])->assignRole('Senior Officer');

        $this->actingAs($officer)->post('/incidents', [
            'incident_type_id' => IncidentType::value('id'),
            'location_id'      => $this->tema->id,
            'building_id'      => $temaBuilding->id,
            'place_id'         => $place->id,
            'severity_id'      => Severity::value('id'),
            'occurred_at'      => now()->subHour()->format('Y-m-d H:i:s'),
        ])->assertSessionHasErrors(['location_id', 'building_id', 'place_id']);
    }

    public function test_assigning_roles_does_not_change_a_users_location_department_or_section(): void
    {
        $admin = User::factory()->create(['location_id' => $this->accra->id])->assignRole('Admin');
        $target = User::factory()->create(['location_id' => $this->accra->id]);

        $this->actingAs($admin)->put("/users/{$target->id}/roles", [
            'role'        => ['Supervisor'],
            'location_id' => $this->tema->id,
            'department'  => 'Real Estate and Security',
            'section'     => 'Security',
        ])->assertRedirect(route('users.index'));

        $target->refresh();
        $this->assertTrue($target->hasRole('Supervisor'));
        $this->assertSame($this->accra->id, $target->location_id);
        $this->assertNull($target->department);
        $this->assertNull($target->section);
    }
}
