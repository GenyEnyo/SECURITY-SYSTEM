<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_local_accounts_cannot_sign_in_while_ldap_is_enabled(): void
    {
        DirectoryEmulator::setup('default');

        User::factory()->create(['username' => 'kwasi'])->assignRole('Super-Admin');

        $this->post('/login', ['username' => 'kwasi', 'password' => 'password'])
            ->assertSessionHasErrors('username');

        $this->assertGuest('web');
    }
}
