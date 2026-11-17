<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use Tests\TestCase;

class LdapLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['ldap.enabled' => true]);
        $this->seed(RoleAndPermissionSeeder::class);
    }

    protected function tearDown(): void
    {
        DirectoryEmulator::tearDown();

        parent::tearDown();
    }

    public function test_directory_user_is_synced_and_logged_in_on_the_web_guard(): void
    {
        $fake = DirectoryEmulator::setup('default');

        $ldapUser = LdapUser::create([
            'cn'             => 'Ama Owusu',
            'samaccountname' => 'aowusu',
            'mail'           => 'ama.owusu@example.com',
            'employeeid'     => '10432',
            'objectguid'     => (string) Str::uuid(),
        ]);

        $fake->actingAs($ldapUser);

        $this->post('/login', ['username' => 'aowusu', 'password' => 'secret'])
            ->assertRedirect(route('access.pending'));

        $user = User::where('username', 'aowusu')->firstOrFail();
        $this->assertSame('Ama Owusu', $user->name);
        $this->assertSame('ama.owusu@example.com', $user->email);
        $this->assertSame('10432', $user->staff_number);
        $this->assertNotNull($user->guid);
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_directory_user_reads_department_and_location_from_staff(): void
    {
        $akosombo = Location::firstOrCreate(['name' => 'Akosombo']);
        Staff::create(['staff_number' => '10432', 'department' => 'VRA - REAL ESTATE & SECURITY SERVICES', 'section' => 'VRA - RED - SECURITY', 'location_name' => 'Akosombo']);

        $fake = DirectoryEmulator::setup('default');

        $ldapUser = LdapUser::create([
            'cn'             => 'Ama Owusu',
            'samaccountname' => 'aowusu',
            'mail'           => 'ama.owusu@example.com',
            'employeeid'     => '10432',
            'objectguid'     => (string) Str::uuid(),
        ]);

        $fake->actingAs($ldapUser);

        $this->post('/login', ['username' => 'aowusu', 'password' => 'secret']);

        $user = User::where('username', 'aowusu')->firstOrFail();
        $this->assertSame('VRA - REAL ESTATE & SECURITY SERVICES', $user->staff->department);
        $this->assertSame('VRA - RED - SECURITY', $user->staff->section);
        $this->assertSame($akosombo->id, $user->locationId());

        // Nothing but the AD fields is stored on users.
        $this->assertFalse(Schema::hasColumns('users', ['department']));
        $this->assertFalse(Schema::hasColumns('users', ['section']));
        $this->assertFalse(Schema::hasColumns('users', ['location_id']));
    }

    public function test_existing_user_without_guid_is_linked_on_first_directory_login(): void
    {
        // A row added before AD login (no guid), already given a role by an admin.
        // Its email differs from AD: the match is by logon name.
        $existing = User::factory()->create([
            'username' => 'aowusu',
            'email'    => 'old.address@example.com',
        ])->assignRole('Security Officer');

        $fake = DirectoryEmulator::setup('default');

        $ldapUser = LdapUser::create([
            'cn'             => 'Ama Owusu',
            'samaccountname' => 'aowusu',
            'mail'           => 'ama.owusu@example.com',
            'employeeid'     => '10432',
            'objectguid'     => (string) Str::uuid(),
        ]);

        $fake->actingAs($ldapUser);

        $this->post('/login', ['username' => 'aowusu', 'password' => 'secret'])
            ->assertRedirect(route('dashboard'));

        $this->assertSame(1, User::where('username', 'aowusu')->count());
        $this->assertSame(1, User::count());
        $this->assertNotNull($existing->fresh()->guid);
        $this->assertSame('ama.owusu@example.com', $existing->fresh()->email);
        $this->assertTrue($existing->fresh()->hasRole('Security Officer'));
        $this->assertAuthenticatedAs($existing->fresh(), 'web');
    }

    public function test_directory_user_without_email_can_log_in(): void
    {
        $fake = DirectoryEmulator::setup('default');

        // Field staff (e.g. guards) often have no email in AD.
        $ldapUser = LdapUser::create([
            'cn'             => 'Kwame Mensah',
            'samaccountname' => 'kmensah',
            'employeeid'     => '20555',
            'objectguid'     => (string) Str::uuid(),
        ]);

        $fake->actingAs($ldapUser);

        $this->post('/login', ['username' => 'kmensah', 'password' => 'secret'])
            ->assertRedirect(route('access.pending'));

        $user = User::where('username', 'kmensah')->firstOrFail();
        $this->assertNull($user->email);
        $this->assertSame('20555', $user->staff_number);
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_local_accounts_cannot_sign_in_while_ldap_is_enabled(): void
    {
        DirectoryEmulator::setup('default');

        User::factory()->create(['username' => 'kwasi'])->assignRole('Super-Admin');

        $this->post('/login', ['username' => 'kwasi', 'password' => 'password'])
            ->assertSessionHasErrors('username');

        $this->assertGuest('web');
    }
}
