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
        // Data is location-scoped, so give the user the location of the seeded building the tests use.
        $location = \App\Models\Location::withoutGlobalScopes()
            ->find(Building::withoutGlobalScopes()->orderBy('id')->value('location_id'));

        return User::factory()->atLocation($location)->create()->assignRole($role);
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

    public function test_security_officer_sees_own_pages_but_not_admin_pages(): void
    {
        $officer = $this->userWithRole('Security Officer');

        foreach (['/dashboard', '/incidents', '/incidents/create', '/kpi/entries', '/deployments', "/buildings/{$this->buildingId()}/deployments/create"] as $url) {
            $this->actingAs($officer)->get($url)->assertOk();
        }

        foreach (['/users', '/roles', '/permissions', '/incidents/all', '/kpi/settings', '/kpi/reports', '/kpi/records', '/kpi/compliance', '/settings/incident-types'] as $url) {
            $this->actingAs($officer)->get($url)->assertForbidden();
        }
    }

    public function test_sidebar_only_shows_permitted_links(): void
    {
        $officer = $this->userWithRole('Security Officer');

        $this->actingAs($officer)->get('/dashboard')
            ->assertSee('Report incident')
            ->assertDontSee('KPI settings')
            ->assertDontSee('Audit logs')
            ->assertSee('Security Officer');
    }

    public function test_reported_incident_belongs_to_the_logged_in_user(): void
    {
        $officer = $this->userWithRole('Security Officer');

        $incident = $this->incidentFor($officer);

        $this->assertSame($officer->id, $incident->user_id);
    }

    public function test_only_permitted_roles_can_acknowledge(): void
    {
        $officer = $this->userWithRole('Security Officer');
        $supervisor = $this->userWithRole('Location Supervisor');
        $head = $this->userWithRole('Head of Security');
        $incident = $this->incidentFor($officer);

        $this->actingAs($officer)->post("/incidents/{$incident->id}/acknowledge")->assertForbidden();
        $this->actingAs($supervisor)->post("/incidents/{$incident->id}/acknowledge")->assertForbidden();
        $this->assertNull($incident->fresh()->acknowledged_at);

        $this->actingAs($head)->post("/incidents/{$incident->id}/acknowledge")->assertRedirect();
        $this->assertNotNull($incident->fresh()->acknowledged_at);
    }

    public function test_officers_cannot_open_incidents_reported_by_others(): void
    {
        $reporter = $this->userWithRole('Security Officer');
        $other = $this->userWithRole('Security Officer');
        $supervisor = $this->userWithRole('Location Supervisor');
        $incident = $this->incidentFor($reporter);

        $this->actingAs($other)->get("/incidents/{$incident->id}")->assertForbidden();
        $this->actingAs($reporter)->get("/incidents/{$incident->id}")->assertOk();
        $this->actingAs($supervisor)->get("/incidents/{$incident->id}")->assertOk();
    }

    public function test_supervisor_can_view_but_not_manage_locations(): void
    {
        $supervisor = $this->userWithRole('Location Supervisor');

        $this->actingAs($supervisor)->get('/locations')->assertOk()->assertDontSee('Add location');
        $this->actingAs($supervisor)->post('/locations', ['name' => 'Annex'])->assertForbidden();
    }

    public function test_data_entry_officer_can_only_enter_data(): void
    {
        $clerk = $this->userWithRole('Data Entry Officer');

        foreach (['/dashboard', '/incidents/create', '/kpi/entries', '/deployments', "/buildings/{$this->buildingId()}/deployments/create", '/my-submissions'] as $url) {
            $this->actingAs($clerk)->get($url)->assertOk();
        }

        foreach (['/kpi/reports', '/kpi/records', '/kpi/compliance', '/incidents/all', '/locations', '/security-companies', '/users'] as $url) {
            $this->actingAs($clerk)->get($url)->assertForbidden();
        }

        // Create-only: cannot edit even their own incident.
        $incident = $this->incidentFor($clerk);
        $this->actingAs($clerk)->get("/incidents/{$incident->id}/edit")->assertForbidden();
    }

    public function test_location_supervisor_views_reports_but_cannot_enter_data(): void
    {
        $supervisor = $this->userWithRole('Location Supervisor');

        foreach (['/dashboard', '/kpi/reports', '/kpi/reports/monthly', '/kpi/records', '/kpi/compliance', '/incidents/all', '/deployments'] as $url) {
            $this->actingAs($supervisor)->get($url)->assertOk();
        }

        foreach (['/kpi/entries', '/incidents/create', "/buildings/{$this->buildingId()}/deployments/create", '/kpi/settings', '/users'] as $url) {
            $this->actingAs($supervisor)->get($url)->assertForbidden();
        }

        $this->assertFalse($supervisor->seesAllLocations());
    }

    public function test_head_of_security_sees_every_location(): void
    {
        $head = $this->userWithRole('Head of Security');
        $otherLocation = \App\Models\Location::withoutGlobalScopes()->where('id', '!=', $head->locationId())->firstOrFail();

        $this->assertTrue($head->seesAllLocations());
        $this->actingAs($head);
        $this->assertTrue(\App\Models\Location::whereKey($otherLocation->id)->exists());

        $this->actingAs($head)->get('/permissions')->assertForbidden();
        $this->actingAs($head)->get('/audit-logs')->assertOk();
    }

    public function test_admin_sees_everything_except_permissions(): void
    {
        $admin = $this->userWithRole('Admin');

        foreach (['/users', '/roles', '/audit-logs', '/kpi/settings', '/kpi/reports', '/incidents/all'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->actingAs($admin)->get('/permissions')->assertForbidden();
        $this->assertTrue($admin->seesAllLocations());
    }

    private function buildingId(): int
    {
        return Building::withoutGlobalScopes()->orderBy('id')->value('id');
    }

    public function test_super_admin_passes_everything(): void
    {
        $admin = User::where('email', 'kwasi@example.com')->firstOrFail();

        foreach (['/dashboard', '/users', '/roles', '/permissions', '/incidents/all', '/kpi/settings', '/kpi/reports', '/kpi/records', '/locations', '/deployments', '/security-companies', '/settings/incident-types', '/settings/severity-levels', '/settings/incident-statuses', '/settings/company-statuses'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_roles_can_be_assigned_through_the_admin_screen(): void
    {
        $admin = $this->userWithRole('Admin');
        $target = User::factory()->create();

        $this->actingAs($admin)->get("/users/{$target->id}/roles")->assertOk();
        $this->actingAs($admin)->put("/users/{$target->id}/roles", ['role' => ['Location Supervisor']])
            ->assertRedirect(route('users.index'));
        $this->assertTrue($target->fresh()->hasRole('Location Supervisor'));

        // Only a Super-Admin may hand out Super-Admin.
        $this->actingAs($admin)->put("/users/{$target->id}/roles", ['role' => ['Super-Admin']])->assertForbidden();
        $this->assertFalse($target->fresh()->hasRole('Super-Admin'));
    }

    public function test_permissions_can_be_synced_to_a_role(): void
    {
        $admin = $this->userWithRole('Admin');
        $role = Role::findByName('Security Officer');

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
