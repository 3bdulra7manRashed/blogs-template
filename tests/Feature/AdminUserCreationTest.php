<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_route_disabled(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(404);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(404);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $response = $this->post('/admin/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'name' => 'New User',
            'is_admin' => false,
        ]);
    }

    public function test_admin_can_create_user_without_password(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $response = $this->post('/admin/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'name' => 'New User',
        ]);

        $user = User::where('email', 'new@example.com')->first();
        $this->assertNotNull($user->password);
    }

    public function test_admin_can_create_admin_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $response = $this->post('/admin/users', [
            'name' => 'Admin User',
            'email' => 'admin2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => true,
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'admin2@example.com',
            'is_admin' => true,
        ]);
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get('/admin/users');
        $response->assertStatus(403);

        $response = $this->get('/admin/users/create');
        $response->assertStatus(403);

        $response = $this->post('/admin/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
        ]);
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get('/admin/users');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/users/create');
        $response->assertRedirect('/login');

        $response = $this->post('/admin/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
        ]);
        $response->assertRedirect('/login');
    }
}

