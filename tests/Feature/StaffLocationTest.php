<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Staff;
use App\Models\User;
use App\Services\StaffDirectory;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class StaffLocationTest extends TestCase
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

    private function admin(): User
    {
        return User::factory()->atLocation($this->accra)->create()->assignRole('Admin');
    }

    /** A staff member whose HR location is wrong ("Tema"), with a user account. */
    private function officerInTema(): User
    {
        return User::factory()->atLocation($this->tema)->create()->assignRole('Location Supervisor');
    }

    private function fakeHrFeed(Staff $staff, string $hrLocation): void
    {
        Http::fake([config('services.staff_api.url') => Http::response([[
            'PERSON_NUMBER' => $staff->staff_number,
            'FULL_NAME'     => 'Kofi Mensah',
            'LOCATION_NAME' => $hrLocation,
        ]])]);
    }

    public function test_admin_correction_moves_what_the_user_sees(): void
    {
        $user = $this->officerInTema();

        $this->actingAs($this->admin())
            ->put(route('settings.staff-locations.update', $user->staff), ['location_id' => $this->accra->id])
            ->assertRedirect();

        $staff = $user->staff->fresh();
        $this->assertSame($this->accra->id, $staff->location_id);
        $this->assertSame('Tema', $staff->location_hr_name_at_override);

        $this->actingAs($user = $user->fresh())->get('/deployments')->assertSee('Head Office')->assertDontSee('KTS');
        $this->assertSame($this->accra->id, $user->locationId());
    }

    public function test_use_hr_location_clears_the_correction(): void
    {
        $user = $this->officerInTema();
        $user->staff->update(['location_id' => $this->accra->id, 'location_hr_name_at_override' => 'Tema']);

        $this->actingAs($this->admin())
            ->put(route('settings.staff-locations.update', $user->staff), ['location_id' => ''])
            ->assertRedirect();

        $this->assertNull($user->staff->fresh()->location_id);
        $this->assertSame($this->tema->id, $user->fresh()->locationId());
    }

    public function test_sync_keeps_the_correction_while_hr_is_unchanged(): void
    {
        $user = $this->officerInTema();
        $user->staff->update(['location_id' => $this->accra->id, 'location_hr_name_at_override' => 'Tema']);

        $this->fakeHrFeed($user->staff, 'TEMA ');
        $this->artisan('staff:sync')->assertSuccessful();

        $this->assertSame($this->accra->id, $user->staff->fresh()->location_id);
        $this->assertSame($this->accra->id, $user->fresh()->locationId());
    }

    public function test_sync_drops_the_correction_once_hr_changes_the_location(): void
    {
        $user = $this->officerInTema();
        $user->staff->update(['location_id' => $this->accra->id, 'location_hr_name_at_override' => 'Tema']);

        // HR finally updates the record (here to Akosombo): HR takes over.
        $this->fakeHrFeed($user->staff, 'Akosombo');
        $this->artisan('staff:sync')
            ->expectsOutputToContain('1 location correction(s) cleared')
            ->assertSuccessful();

        $staff = $user->staff->fresh();
        $this->assertNull($staff->location_id);
        $this->assertNull($staff->location_hr_name_at_override);
        $this->assertSame(Location::where('name', 'Akosombo')->value('id'), $user->fresh()->locationId());

        $this->assertTrue(Activity::where('subject_type', Staff::class)->where('subject_id', $staff->id)->where('event', 'updated')->exists());
    }

    public function test_unrecognised_filter_lists_staff_with_no_matching_location(): void
    {
        Staff::create(['staff_number' => '70001', 'full_name' => 'Abena Unknown', 'location_name' => 'Damango']);
        Staff::create(['staff_number' => '70002', 'full_name' => 'Yaw Known', 'location_name' => 'Accra']);
        Staff::create(['staff_number' => '70003', 'full_name' => 'Esi Fixed', 'location_name' => 'Damango', 'location_id' => $this->accra->id]);

        $this->actingAs($this->admin())
            ->get(route('settings.staff-locations.index', ['filter' => 'unrecognised']))
            ->assertOk()
            ->assertSee('Abena Unknown')
            ->assertDontSee('Yaw Known')
            ->assertDontSee('Esi Fixed');
    }

    public function test_only_permitted_roles_can_correct_locations(): void
    {
        $user = $this->officerInTema();

        foreach (['Location Supervisor', 'Security Officer', 'Data Entry Officer'] as $role) {
            $other = User::factory()->atLocation($this->tema)->create()->assignRole($role);

            $this->actingAs($other)->get(route('settings.staff-locations.index'))->assertForbidden();
            $this->actingAs($other)
                ->put(route('settings.staff-locations.update', $user->staff), ['location_id' => $this->accra->id])
                ->assertForbidden();
        }

        $head = User::factory()->atLocation($this->tema)->create()->assignRole('Head of Security');
        $this->actingAs($head)->get(route('settings.staff-locations.index'))->assertOk();
    }

    public function test_correction_is_written_to_the_audit_log(): void
    {
        $user = $this->officerInTema();
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('settings.staff-locations.update', $user->staff), ['location_id' => $this->accra->id]);

        $entry = Activity::where('subject_type', Staff::class)->where('subject_id', $user->staff->id)->where('event', 'updated')->latest('id')->firstOrFail();
        $this->assertSame($admin->id, $entry->causer_id);
        $this->assertSame($this->accra->id, $entry->properties['attributes']['location_id']);
    }
}
