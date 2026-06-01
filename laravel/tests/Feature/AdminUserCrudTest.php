<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
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
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    }

    public function test_guest_cannot_access_admin_users_module(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_admin_users_module(): void
    {
        $nonAdmin = $this->createUserWithRole(Role::CLIENT);

        $response = $this->actingAs($nonAdmin)->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_users(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $userRole = Role::query()->firstOrCreate(['name' => Role::CLIENT]);

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
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_admin_user_creation_validation_rejects_invalid_payloads(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $existing = User::factory()->create([
            'email' => 'taken@example.com',
            'role_id' => Role::query()->where('name', Role::CLIENT)->firstOrFail()->id,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => '',
                'last_name' => 'User',
                'email' => 'not-an-email',
                'role_id' => 999,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors(['name', 'email', 'role_id', 'password']);

        $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'Duplicate',
                'last_name' => 'User',
                'email' => $existing->email,
                'role_id' => Role::query()->where('name', Role::CLIENT)->firstOrFail()->id,
                'password' => 'StrongPass123!',
                'password_confirmation' => 'StrongPass123!',
            ])
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_create_user_with_custom_role(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $customRole = Role::query()->create(['name' => 'operator']);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Custom',
                'last_name' => 'Role',
                'email' => 'custom.role@example.com',
                'role_id' => $customRole->id,
                'password' => 'StrongPass123!',
                'password_confirmation' => 'StrongPass123!',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'custom.role@example.com',
            'role_id' => $customRole->id,
        ]);
    }

    public function test_admin_can_update_user_password_and_it_is_hashed(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $user = $this->createUserWithRole(Role::CLIENT);
        $oldPassword = $user->password;

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'password' => 'NewStrongPass123!',
                'password_confirmation' => 'NewStrongPass123!',
            ])
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();

        $this->assertNotSame($oldPassword, $user->password);
        $this->assertTrue(Hash::check('NewStrongPass123!', $user->password));
    }

    public function test_admin_user_actions_require_specific_permissions(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $clientRole = Role::query()->where('name', Role::CLIENT)->firstOrFail();
        $target = $this->createUserWithRole(Role::CLIENT);

        $admin->role->permissions()->sync([]);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'No',
                'last_name' => 'Permission',
                'email' => 'no.permission@example.com',
                'role_id' => $clientRole->id,
                'password' => 'StrongPass123!',
                'password_confirmation' => 'StrongPass123!',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name' => 'No',
                'last_name' => 'Permission',
                'email' => $target->email,
                'role_id' => $clientRole->id,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $target))
            ->assertForbidden();
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
