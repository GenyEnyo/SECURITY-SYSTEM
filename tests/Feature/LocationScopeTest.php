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
        return User::factory()->atLocation($location)->create()->assignRole('Location Supervisor');
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
        $officer = User::factory()->atLocation($this->accra)->create()->assignRole('Security Officer');

        $this->actingAs($officer)->post('/incidents', [
            'incident_type_id' => IncidentType::value('id'),
            'location_id'      => $this->tema->id,
            'building_id'      => $temaBuilding->id,
            'place_id'         => $place->id,
            'severity_id'      => Severity::value('id'),
            'occurred_at'      => now()->subHour()->format('Y-m-d H:i:s'),
        ])->assertSessionHasErrors(['location_id', 'building_id', 'place_id']);
    }

    public function test_assigning_roles_does_not_change_a_users_staff_details(): void
    {
        $admin = User::factory()->atLocation($this->accra)->create()->assignRole('Admin');
        $target = User::factory()->atLocation($this->accra)->create();

        $this->actingAs($admin)->put("/users/{$target->id}/roles", [
            'role'          => ['Location Supervisor'],
            'staff_number'  => '999',
            'location_name' => 'Tema',
        ])->assertRedirect(route('users.index'));

        $target = $target->fresh();
        $this->assertTrue($target->hasRole('Location Supervisor'));
        $this->assertSame($this->accra->id, $target->locationId());
        $this->assertNotSame('999', $target->staff_number);
    }

    public function test_moving_a_staff_record_moves_what_the_user_sees(): void
    {
        $user = $this->supervisorAt($this->accra);

        $this->actingAs($user)->get('/deployments')->assertSee('Head Office')->assertDontSee('KTS');

        // HR moves them to Tema: no change to the users table needed.
        $user->staff->update(['location_name' => 'Tema']);
        $user = $user->fresh();

        $this->actingAs($user)->get('/deployments')->assertSee('KTS')->assertDontSee('Head Office');
    }

    public function test_user_with_no_staff_record_sees_nothing(): void
    {
        $user = User::factory()->create(['staff_number' => '404404'])->assignRole('Location Supervisor');

        $this->actingAs($user)->get('/deployments')
            ->assertOk()
            ->assertDontSee('Head Office')
            ->assertDontSee('KTS');
    }
}
