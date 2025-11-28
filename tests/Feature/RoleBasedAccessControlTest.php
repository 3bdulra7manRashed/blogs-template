<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles - مدير النظام and مشرف
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'moderator', 'guard_name' => 'web']);
    }

    /** @test */
    public function super_admin_can_access_user_management()
    {
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
            'is_admin' => true,
        ]);
        $superAdmin->assignRole('admin');

        $response = $this->actingAs($superAdmin)->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function moderator_cannot_access_user_management()
    {
        $moderator = User::factory()->create([
            'is_super_admin' => false,
            'is_admin' => true,
        ]);
        $moderator->assignRole('moderator');

        $response = $this->actingAs($moderator)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function moderator_can_access_content_management()
    {
        $moderator = User::factory()->create([
            'is_super_admin' => false,
            'is_admin' => true,
        ]);
        $moderator->assignRole('moderator');

        $response = $this->actingAs($moderator)->get(route('admin.posts.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function super_admin_can_create_users()
    {
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
            'is_admin' => true,
        ]);
        $superAdmin->assignRole('admin');

        $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function moderator_cannot_create_users()
    {
        $moderator = User::factory()->create([
            'is_super_admin' => false,
            'is_admin' => true,
        ]);
        $moderator->assignRole('moderator');

        $response = $this->actingAs($moderator)->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => true,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function only_super_admin_can_create_other_super_admins()
    {
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
            'is_admin' => true,
        ]);
        $superAdmin->assignRole('admin');

        $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'Another Super Admin',
            'email' => 'superadmin2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'superadmin2@example.com',
            'is_super_admin' => true,
        ]);
    }

    /** @test */
    public function new_users_default_to_moderator_role()
    {
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
            'is_admin' => true,
        ]);
        $superAdmin->assignRole('admin');

        $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'New Moderator',
            'email' => 'moderator@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => true, // This means moderator
        ]);

        $user = User::where('email', 'moderator@example.com')->first();
        
        $this->assertTrue($user->hasRole('moderator'));
        $this->assertFalse($user->is_super_admin);
    }

    /** @test */
    public function viewer_and_editor_roles_do_not_exist()
    {
        $this->assertFalse(Role::where('name', 'user')->exists());
        $this->assertFalse(Role::where('name', 'viewer')->exists());
        $this->assertFalse(Role::where('name', 'editor')->exists());
    }

    /** @test */
    public function only_admin_and_moderator_roles_exist()
    {
        $roles = Role::pluck('name')->toArray();
        
        $this->assertContains('admin', $roles);
        $this->assertContains('moderator', $roles);
        $this->assertCount(2, $roles);
    }

    /** @test */
    public function deleted_user_placeholder_cannot_be_deleted()
    {
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
            'is_admin' => true,
        ]);
        $superAdmin->assignRole('admin');

        // Create deleted user placeholder
        $deletedUser = User::factory()->create([
            'email' => config('app.deleted_user_email', 'deleted-user@local'),
            'name' => 'Deleted User',
        ]);

        $response = $this->actingAs($superAdmin)->delete(route('admin.users.destroy', $deletedUser));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $deletedUser->id]);
    }
}

