<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_can_login_and_only_access_dashboard_and_transactions(): void
    {
        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'role' => User::ROLE_USER,
        ]);

        $this->post('/login/do', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/admin/dashboard');

        $this->assertAuthenticatedAs($user);

        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Hallo, User!')
            ->assertDontSee('Hallo, AdminLTE3!')
            ->assertSee(route('transaction.index'))
            ->assertDontSee(route('user.index'))
            ->assertDontSee(route('category.index'))
            ->assertDontSee(route('product.index'));

        $this->get(route('transaction.index'))->assertOk();
        $this->get(route('user.index'))->assertForbidden();
        $this->get(route('user.create'))->assertForbidden();
        $this->post(route('user.store'), [
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
            'password' => 'password123',
            're_password' => 'password123',
            'role' => User::ROLE_USER,
        ])->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'blocked@example.com']);
        $this->get(route('category.index'))->assertForbidden();
        $this->get(route('product.index'))->assertForbidden();
    }

    public function test_admin_can_access_all_menus(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Hallo, Admin!')
            ->assertSee(route('user.index'))
            ->assertSee(route('category.index'))
            ->assertSee(route('product.index'))
            ->assertSee(route('transaction.index'));

        $this->get(route('user.index'))->assertOk();
        $this->get(route('user.create'))->assertOk();
        $this->get(route('category.index'))->assertOk();
        $this->get(route('product.index'))->assertOk();
    }

    public function test_admin_can_create_user_with_selected_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('user.store'), [
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                're_password' => 'password123',
                'role' => User::ROLE_USER,
            ])
            ->assertRedirect(route('user.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_admin_cannot_create_user_with_invalid_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('user.store'), [
                'name' => 'Invalid Role',
                'email' => 'invalid@example.com',
                'password' => 'password123',
                're_password' => 'password123',
                'role' => 'superadmin',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'invalid@example.com']);
    }
}
