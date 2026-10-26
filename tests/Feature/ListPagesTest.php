<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('email', 'kwasi@example.com')->firstOrFail());
    }

    public function test_list_pages_have_table_search(): void
    {
        foreach (['/users', '/roles', '/permissions', '/security-companies'] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('data-datatable', false)
                ->assertSee('theme/plugins/datatables/dataTables.min.js', false);
        }
    }

    public function test_modal_forms_post_inside_the_app_folder(): void
    {
        // A root-relative action ("/buildings/5") escapes the subfolder the app is
        // hosted in (localhost/SECURITY-SYSTEM, abb.cse.com/security_system).
        $building = Building::firstOrFail();

        $pages = [
            '/locations', '/kpi/settings', '/security-companies',
            "/buildings/{$building->id}/places", "/buildings/{$building->id}/deployments",
            '/settings/incident-types', '/settings/severity-levels',
            '/settings/incident-statuses', '/settings/company-statuses',
            '/roles', '/permissions',
        ];

        foreach ($pages as $url) {
            $this->get($url)
                ->assertOk()
                ->assertDontSee('action = `/', false);
        }
    }
}
