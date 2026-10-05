<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\IncidentOccurrence;
use App\Models\IncidentType;
use App\Models\Place;
use App\Models\Severity;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create()->assignRole($role);
    }

    private function incidentFor(User $reporter): IncidentOccurrence
    {
        $building = Building::firstOrFail();
        $place = Place::firstOrCreate(['building_id' => $building->id, 'name' => 'Main gate']);

        $this->actingAs($reporter)->post('/incidents', [
            'incident_type_id' => IncidentType::value('id'),
            'location_id'      => $building->location_id,
            'building_id'      => $building->id,
            'place_id'         => $place->id,
            'severity_id'      => Severity::value('id'),
            'occurred_at'      => now()->subHour()->format('Y-m-d H:i:s'),
            'description'      => 'Gate left open',
        ])->assertSessionHasNoErrors();

        return IncidentOccurrence::latest('id')->firstOrFail();
    }

    public function test_senior_officer_sees_own_pages_but_not_admin_pages(): void
    {
        $officer = $this->userWithRole('Senior Officer');

        $this->actingAs($officer)->get('/dashboard')->assertOk();
        $this->actingAs($officer)->get('/incidents')->assertOk();
        $this->actingAs($officer)->get('/incidents/create')->assertOk();

        foreach (['/users', '/roles', '/permissions', '/incidents/all', '/kpi/settings', '/kpi/reports', '/locations', '/settings/incident-types'] as $url) {
            $this->actingAs($officer)->get($url)->assertForbidden();
        }
    }

    public function test_sidebar_only_shows_permitted_links(): void
    {
        $officer = $this->userWithRole('Senior Officer');

        $this->actingAs($officer)->get('/dashboard')
            ->assertSee('Report incident')
            ->assertDontSee('Security companies')
            ->assertDontSee('KPI settings')
            ->assertSee('Senior Officer');
    }

    public function test_reported_incident_belongs_to_the_logged_in_user(): void
    {
        $officer = $this->userWithRole('Senior Officer');

        $incident = $this->incidentFor($officer);

        $this->assertSame($officer->id, $incident->user_id);
    }

    public function test_only_permitted_roles_can_acknowledge(): void
    {
        $officer = $this->userWithRole('Senior Officer');
        $supervisor = $this->userWithRole('Supervisor');
        $incident = $this->incidentFor($officer);

        $this->actingAs($officer)->post("/incidents/{$incident->id}/acknowledge")->assertForbidden();
        $this->assertNull($incident->fresh()->acknowledged_at);

        $this->actingAs($supervisor)->post("/incidents/{$incident->id}/acknowledge")->assertRedirect();
        $this->assertNotNull($incident->fresh()->acknowledged_at);
    }

    public function test_officers_cannot_open_incidents_reported_by_others(): void
    {
        $reporter = $this->userWithRole('Senior Officer');
        $other = $this->userWithRole('Senior Officer');
        $supervisor = $this->userWithRole('Supervisor');
        $incident = $this->incidentFor($reporter);

        $this->actingAs($other)->get("/incidents/{$incident->id}")->assertForbidden();
        $this->actingAs($reporter)->get("/incidents/{$incident->id}")->assertOk();
        $this->actingAs($supervisor)->get("/incidents/{$incident->id}")->assertOk();
    }

    public function test_supervisor_can_view_but_not_manage_locations(): void
    {
        $supervisor = $this->userWithRole('Supervisor');

        $this->actingAs($supervisor)->get('/locations')->assertOk()->assertDontSee('Add location');
        $this->actingAs($supervisor)->post('/locations', ['name' => 'Annex'])->assertForbidden();
    }

    public function test_super_admin_passes_everything(): void
    {
        $admin = User::where('email', 'kwasi@example.com')->firstOrFail();

        foreach (['/dashboard', '/users', '/roles', '/permissions', '/incidents/all', '/kpi/settings', '/kpi/reports', '/kpi/records', '/locations', '/deployments', '/security-companies', '/settings/incident-types', '/settings/severity-levels'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_roles_can_be_assigned_through_the_admin_screen(): void
    {
        $admin = $this->userWithRole('Admin');
        $target = User::factory()->create();

        $this->actingAs($admin)->get("/users/{$target->id}/roles")->assertOk();
        $this->actingAs($admin)->put("/users/{$target->id}/roles", ['role' => ['Supervisor']])
            ->assertRedirect(route('users.index'));
        $this->assertTrue($target->fresh()->hasRole('Supervisor'));

        // Only a Super-Admin may hand out Super-Admin.
        $this->actingAs($admin)->put("/users/{$target->id}/roles", ['role' => ['Super-Admin']])->assertForbidden();
        $this->assertFalse($target->fresh()->hasRole('Super-Admin'));
    }

    public function test_permissions_can_be_synced_to_a_role(): void
    {
        $admin = $this->userWithRole('Admin');
        $role = Role::findByName('Senior Officer');

        $this->actingAs($admin)->get("/roles/{$role->id}/permissions")->assertOk();
        $this->actingAs($admin)->put("/roles/{$role->id}/permissions", ['permission' => ['View Dashboard', 'View KPI Reports']])
            ->assertRedirect(route('roles.index'));

        $this->assertEqualsCanonicalizing(['View Dashboard', 'View KPI Reports'], $role->fresh()->permissions->pluck('name')->all());
    }

    public function test_assign_role_command(): void
    {
        $user = User::factory()->create(['username' => 'aowusu']);

        $this->artisan('user:assign-role', ['username' => 'aowusu', 'role' => 'Super-Admin'])->assertSuccessful();

        $this->assertTrue($user->fresh()->hasRole('Super-Admin'));
    }
}
