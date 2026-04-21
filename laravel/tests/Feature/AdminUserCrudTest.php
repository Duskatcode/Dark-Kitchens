<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_cannot_access_admin_users_module(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_admin_users_module(): void
    {
        $nonAdmin = $this->createUserWithRole('User');

        $response = $this->actingAs($nonAdmin)->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_users(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $userRole = Role::query()->firstOrCreate(['name' => 'User']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Demo',
                'last_name' => 'User',
                'email' => 'demo.user@example.com',
                'role_id' => $userRole->id,
                'password' => 'StrongPass123!',
                'password_confirmation' => 'StrongPass123!',
            ])
            ->assertRedirect(route('admin.users.index'));

        $createdUser = User::query()->where('email', 'demo.user@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('StrongPass123!', $createdUser->password));

        $this->actingAs($admin)
            ->put(route('admin.users.update', $createdUser), [
                'name' => 'Demo Updated',
                'last_name' => 'User Updated',
                'email' => 'demo.user@example.com',
                'role_id' => $userRole->id,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.users.index'));

        $updatedUser = $createdUser->fresh();

        $this->assertSame('Demo Updated', $updatedUser->name);
        $this->assertSame('User Updated', $updatedUser->last_name);
        $this->assertTrue(Hash::check('StrongPass123!', $updatedUser->password));

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $updatedUser))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', [
            'email' => 'demo.user@example.com',
        ]);
    }

    public function test_admin_cannot_delete_himself(): void
    {
        $admin = $this->createUserWithRole('Admin');

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
