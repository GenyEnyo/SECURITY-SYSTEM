<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['ldap.enabled' => false]);
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create()->assignRole($role);
    }

    public function test_model_changes_are_logged_with_the_acting_user(): void
    {
        $admin = $this->userWithRole('Admin');

        $this->actingAs($admin)->post('/locations', ['name' => 'Annex'])->assertSessionHasNoErrors();
        $location = Location::where('name', 'Annex')->firstOrFail();
        $this->actingAs($admin)->put("/locations/{$location->id}", ['name' => 'Annex B'])->assertSessionHasNoErrors();
        $this->actingAs($admin)->delete("/locations/{$location->id}");

        $entries = Activity::where('log_name', 'location')->orderBy('id')->get();

        $this->assertSame(['created', 'updated', 'deleted'], $entries->pluck('description')->all());
        $this->assertTrue($entries->every(fn ($entry) => $entry->causer_id === $admin->id));
        $this->assertSame('Annex', $entries[1]->properties['old']['name']);
        $this->assertSame('Annex B', $entries[1]->properties['attributes']['name']);
    }

    public function test_passwords_are_never_logged(): void
    {
        $user = User::factory()->create();
        $user->update(['name' => 'Renamed', 'password' => 'new-secret']);

        $entries = Activity::where('log_name', 'user')->get();

        $this->assertNotEmpty($entries);
        foreach ($entries as $entry) {
            $this->assertArrayNotHasKey('password', $entry->properties['attributes'] ?? []);
            $this->assertArrayNotHasKey('password', $entry->properties['old'] ?? []);
            $this->assertStringNotContainsString('new-secret', $entry->properties->toJson());
        }
    }

    public function test_sign_in_events_are_logged(): void
    {
        $user = User::factory()->create(['username' => 'kofi'])->assignRole('Senior Officer');

        $this->post('/login', ['username' => 'kofi', 'password' => 'wrong']);
        $this->post('/login', ['username' => 'kofi', 'password' => 'password']);
        $this->post('/logout');

        $entries = Activity::where('log_name', 'auth')->orderBy('id')->get();

        $this->assertSame(['failed login', 'logged in', 'logged out'], $entries->pluck('description')->all());
        $this->assertNull($entries[0]->causer_id);
        $this->assertSame('kofi', $entries[0]->properties['username']);
        $this->assertStringNotContainsString('wrong', $entries[0]->properties->toJson());
        $this->assertSame('local', $entries[1]->properties['via']);
        $this->assertSame($user->id, $entries[1]->causer_id);
        $this->assertSame($user->id, $entries[2]->causer_id);
    }

    public function test_role_changes_are_logged(): void
    {
        $admin = $this->userWithRole('Admin');
        $target = $this->userWithRole('Senior Officer');

        $this->actingAs($admin)->put("/users/{$target->id}/roles", ['role' => ['Supervisor']]);

        $entry = Activity::where('log_name', 'access')->where('description', 'roles changed')->firstOrFail();

        $this->assertSame($admin->id, $entry->causer_id);
        $this->assertSame($target->id, $entry->subject_id);
        $this->assertSame(['Senior Officer'], $entry->properties['old']['roles']);
        $this->assertSame(['Supervisor'], $entry->properties['attributes']['roles']);
    }

    public function test_audit_page_is_restricted(): void
    {
        $this->get('/audit-logs')->assertRedirect(route('login'));

        $admin = $this->userWithRole('Admin');
        $this->actingAs($admin)->post('/locations', ['name' => 'Annex']);

        $this->actingAs($admin)->get('/audit-logs')->assertOk()->assertSee('Annex')->assertSee('Created');
        $this->actingAs($admin)->get('/audit-logs?log=auth')->assertOk()->assertDontSee('Annex');
        $this->actingAs($this->userWithRole('Super-Admin'))->get('/audit-logs')->assertOk();
        $this->actingAs($this->userWithRole('Supervisor'))->get('/audit-logs')->assertForbidden();
        $this->actingAs($this->userWithRole('Senior Officer'))->get('/dashboard')->assertDontSee('Audit logs');
    }
}
