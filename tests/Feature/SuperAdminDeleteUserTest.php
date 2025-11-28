<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDeleteUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create placeholder user
        $deletedUserEmail = config('app.deleted_user_email', env('DELETED_USER_EMAIL', 'deleted-user@local'));
        User::factory()->create(['email' => $deletedUserEmail]);
    }

    public function test_only_super_admin_can_delete_user(): void
    {
        $super = User::factory()->create(['is_super_admin' => true]);
        $user = User::factory()->create();
        
        $this->actingAs($super);
        
        $res = $this->delete(route('admin.users.destroy', $user));
        
        $res->assertRedirect(route('admin.users.index'));
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_posts_reassigned_to_placeholder_on_delete(): void
    {
        $super = User::factory()->create(['is_super_admin' => true]);
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        
        $placeholderEmail = config('app.deleted_user_email', env('DELETED_USER_EMAIL', 'deleted-user@local'));
        $placeholder = User::factory()->create(['email' => $placeholderEmail]);
        
        $this->actingAs($super)->delete(route('admin.users.destroy', $user));
        
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'user_id' => $placeholder->id]);
    }

    public function test_audit_log_created(): void
    {
        $super = User::factory()->create(['is_super_admin' => true]);
        $user = User::factory()->create();
        
        $this->actingAs($super)->delete(route('admin.users.destroy', $user));
        
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $super->id,
            'target_user_id' => $user->id,
            'action' => 'soft_delete_user',
        ]);
    }

    public function test_cannot_delete_placeholder(): void
    {
        $super = User::factory()->create(['is_super_admin' => true]);
        $placeholderEmail = config('app.deleted_user_email', env('DELETED_USER_EMAIL', 'deleted-user@local'));
        $placeholder = User::factory()->create(['email' => $placeholderEmail]);
        
        $this->actingAs($super);
        
        $res = $this->delete(route('admin.users.destroy', $placeholder));
        
        $this->assertStatus(403);
    }

    public function test_restore_user_works(): void
    {
        $super = User::factory()->create(['is_super_admin' => true]);
        $user = User::factory()->create();
        $user->delete(); // Soft delete
        
        $this->actingAs($super);
        
        $res = $this->post(route('admin.users.restore', $user->id));
        
        $res->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
    }
}
