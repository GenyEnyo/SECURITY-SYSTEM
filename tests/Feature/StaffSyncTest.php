<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Staff;
use App\Models\User;
use App\Services\StaffDirectory;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StaffSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    private function record(array $overrides = []): array
    {
        return array_merge([
            'PERSON_NUMBER'     => '10432',
            'FULL_NAME'         => 'Owusu, Ms. Ama',
            'FIRST_NAME'        => 'Ama',
            'MIDDLE_NAMES'      => null,
            'LAST_NAME'         => 'Owusu',
            'TITLE'             => 'Ms.',
            'DEPARTMENT_NAME'   => 'VRA - REAL ESTATE & SECURITY SERVICES',
            'SECTION'           => 'VRA - RED - SECURITY',
            'JOB_NAME'          => 'Security Officer',
            'POSITION_NAME'     => 'Senior Security Officer',
            'SUPERVISOR_NUMBER' => '9001',
            'EMAIL_ADDRESS'     => 'Ama.Owusu@example.com',
            'LOCATION_NAME'     => 'Akosombo',
        ], $overrides);
    }

    private function fakeFeed(array $records, int $status = 200): void
    {
        Http::fake([config('services.staff_api.url') => Http::response($records, $status)]);
    }

    public function test_sync_stores_updates_and_removes_staff(): void
    {
        $this->fakeFeed([$this->record(), $this->record(['PERSON_NUMBER' => '20001', 'EMAIL_ADDRESS' => 'kofi@example.com'])]);

        $this->artisan('staff:sync')->assertSuccessful();

        $this->assertSame(2, Staff::count());
        $this->assertSame('Akosombo', Staff::where('staff_number', '10432')->value('location_name'));

        Http::swap(new \Illuminate\Http\Client\Factory);
        $this->fakeFeed([$this->record(['LOCATION_NAME' => 'Tema'])]);

        $this->artisan('staff:sync')->assertSuccessful();

        $this->assertSame(1, Staff::count());
        $this->assertSame('Tema', Staff::where('staff_number', '10432')->value('location_name'));
    }

    public function test_failed_or_empty_feed_leaves_the_stored_list_unchanged(): void
    {
        Staff::create(['staff_number' => '10432', 'full_name' => 'Ama Owusu']);

        $this->fakeFeed([], 500);
        $this->artisan('staff:sync')->assertFailed();

        Http::swap(new \Illuminate\Http\Client\Factory);
        $this->fakeFeed([]);
        $this->artisan('staff:sync')->assertFailed();

        $this->assertSame(1, Staff::count());
    }

    public function test_sync_fills_blank_user_details_from_the_staff_list(): void
    {
        $user = User::factory()->create(['staff_number' => '10432']);
        $byEmail = User::factory()->create(['email' => 'kofi@example.com']);

        $this->fakeFeed([$this->record(), $this->record(['PERSON_NUMBER' => '20001', 'EMAIL_ADDRESS' => 'Kofi@Example.com', 'LOCATION_NAME' => 'accra'])]);

        $this->artisan('staff:sync')->assertSuccessful();

        $user->refresh();
        $this->assertSame('VRA - REAL ESTATE & SECURITY SERVICES', $user->department);
        $this->assertSame('VRA - RED - SECURITY', $user->section);
        $this->assertSame(Location::where('name', 'Akosombo')->value('id'), $user->location_id);

        $byEmail->refresh();
        $this->assertSame('20001', $byEmail->staff_number);
        $this->assertSame(Location::where('name', 'Accra')->value('id'), $byEmail->location_id);
    }

    public function test_fill_never_overwrites_details_that_are_already_set(): void
    {
        Staff::create(['staff_number' => '10432', 'department' => 'VRA - FINANCE & INVESTMENT', 'section' => 'VRA - FIV - TREASURY', 'location_name' => 'Akosombo']);

        $tema = Location::where('name', 'Tema')->firstOrFail();
        $user = User::factory()->create(['staff_number' => '10432', 'department' => 'Security', 'section' => 'Patrol', 'location_id' => $tema->id]);

        app(StaffDirectory::class)->fill($user);

        $user->refresh();
        $this->assertSame('Security', $user->department);
        $this->assertSame('Patrol', $user->section);
        $this->assertSame($tema->id, $user->location_id);
    }

    public function test_unknown_staff_location_is_left_unset(): void
    {
        Staff::create(['staff_number' => '10432', 'department' => 'NED - COMMERCIAL', 'location_name' => 'Tamale']);

        $user = User::factory()->create(['staff_number' => '10432']);

        app(StaffDirectory::class)->fill($user);

        $user->refresh();
        $this->assertSame('NED - COMMERCIAL', $user->department);
        $this->assertNull($user->location_id);
    }

    public function test_assign_roles_page_shows_user_details_as_text_only(): void
    {
        $admin = User::factory()->create()->assignRole('Super-Admin');
        $user = User::factory()->create(['department' => 'NED - COMMERCIAL', 'section' => 'NED - NCD - CUSTOMER SERVICES']);

        $this->actingAs($admin)->get(route('users.roles.edit', $user))
            ->assertOk()
            ->assertSee('NED - COMMERCIAL')
            ->assertSee('NED - NCD - CUSTOMER SERVICES')
            ->assertDontSee('name="location_id"', false)
            ->assertDontSee('name="department"', false)
            ->assertDontSee('name="section"', false);
    }
}
