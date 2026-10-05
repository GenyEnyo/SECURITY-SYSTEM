<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['ldap.enabled' => false]);
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/incidents')->assertRedirect(route('login'));
        $this->get('/users')->assertRedirect(route('login'));
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('Local login mode');
    }

    public function test_local_login_with_username(): void
    {
        $user = User::factory()->create(['username' => 'kofi'])->assignRole('Senior Officer');

        $this->post('/login', ['username' => 'kofi', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_local_login_with_email(): void
    {
        $user = User::factory()->create()->assignRole('Senior Officer');

        $this->post('/login', ['username' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_local_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['username' => 'kofi']);

        $this->from('/login')
            ->post('/login', ['username' => 'kofi', 'password' => 'wrong'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_login_is_throttled(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->post('/login', ['username' => 'nobody', 'password' => 'wrong'])->assertStatus(302);
        }

        $this->post('/login', ['username' => 'nobody', 'password' => 'wrong'])->assertStatus(429);
    }

    public function test_user_without_a_role_lands_on_access_pending(): void
    {
        User::factory()->create(['username' => 'newbie']);

        $this->post('/login', ['username' => 'newbie', 'password' => 'password'])
            ->assertRedirect(route('access.pending'));

        $this->get('/access-pending')->assertOk()->assertSee('Access pending');
        $this->get('/dashboard')->assertForbidden();
    }

    public function test_logout_ends_the_session(): void
    {
        $user = User::factory()->create()->assignRole('Senior Officer');

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect(route('login'));
    }
}
