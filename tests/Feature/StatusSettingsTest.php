<?php

namespace Tests\Feature;

use App\Models\IncidentStatus;
use App\Models\SecurityCompany;
use App\Models\SecurityCompanyStatus;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'kwasi@example.com')->firstOrFail();
    }

    public function test_incident_statuses_can_be_added_but_system_ones_are_locked(): void
    {
        $this->actingAs($this->admin)->post('/settings/incident-statuses', ['name' => 'Under investigation'])
            ->assertRedirect(route('settings.incident-statuses.index'));
        $this->assertDatabaseHas('incident_statuses', ['name' => 'Under investigation']);

        $reported = IncidentStatus::where('name', 'Reported')->firstOrFail();

        $this->actingAs($this->admin)->put("/settings/incident-statuses/{$reported->id}", ['name' => 'New'])
            ->assertSessionHas('error');
        $this->actingAs($this->admin)->delete("/settings/incident-statuses/{$reported->id}")
            ->assertSessionHas('error');
        $this->assertDatabaseHas('incident_statuses', ['id' => $reported->id, 'name' => 'Reported']);
    }

    public function test_company_status_rename_carries_over_to_companies(): void
    {
        $this->actingAs($this->admin)->post('/settings/company-statuses', ['name' => 'suspended'])
            ->assertRedirect(route('settings.company-statuses.index'));

        $status = SecurityCompanyStatus::where('name', 'suspended')->firstOrFail();
        $company = SecurityCompany::firstOrFail();
        $company->update(['status' => 'suspended']);

        $this->actingAs($this->admin)->delete("/settings/company-statuses/{$status->id}")->assertSessionHas('error');

        $this->actingAs($this->admin)->put("/settings/company-statuses/{$status->id}", ['name' => 'on hold'])
            ->assertSessionHasNoErrors();
        $this->assertSame('on hold', $company->fresh()->status);

        $active = SecurityCompanyStatus::where('name', 'active')->firstOrFail();
        $this->actingAs($this->admin)->delete("/settings/company-statuses/{$active->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('security_company_statuses', ['name' => 'active']);
    }

    public function test_settings_pages_need_their_permission(): void
    {
        $officer = User::factory()->create()->assignRole('Senior Officer');

        $this->actingAs($officer)->get('/settings/incident-statuses')->assertForbidden();
        $this->actingAs($officer)->get('/settings/company-statuses')->assertForbidden();
    }
}
