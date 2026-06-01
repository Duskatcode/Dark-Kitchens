<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminRoleCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    }

    public function test_guest_cannot_access_admin_roles_module(): void
    {
        $response = $this->get(route('admin.roles.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_client_cannot_access_admin_roles_module(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($client)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_cook_cannot_access_admin_roles_module(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);

        $this->actingAs($cook)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_roles_module(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->get(route('admin.roles.index'))
            ->assertOk();
    }

    public function test_admin_can_create_valid_non_core_role(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $permission = Permission::query()->where('key', 'admin.users.view')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name' => 'manager',
                'permissions' => [$permission->id],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'name' => 'manager',
        ]);

        $role = Role::query()->where('name', 'manager')->firstOrFail();

        $this->assertTrue($role->permissions()->whereKey($permission->id)->exists());
    }

    public function test_admin_cannot_create_role_with_uppercase_name(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->from(route('admin.roles.create'))
            ->post(route('admin.roles.store'), [
                'name' => 'Manager',
            ])
            ->assertRedirect(route('admin.roles.create'))
            ->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('roles', [
            'name' => 'Manager',
        ]);
    }

    public function test_admin_roles_manage_permissions_is_required_to_create_role_with_permissions(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $admin->role->permissions()->sync(
            Permission::query()
                ->whereIn('key', ['admin.roles.create', 'admin.roles.view'])
                ->pluck('id')
        );
        $permission = Permission::query()->where('key', 'admin.users.view')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name' => 'support',
                'permissions' => [$permission->id],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('roles', [
            'name' => 'support',
        ]);
    }

    public function test_admin_cannot_rename_core_roles(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        foreach (Role::coreRoles() as $coreRoleName) {
            $role = Role::query()->firstOrCreate(['name' => $coreRoleName]);

            $this->actingAs($admin)
                ->put(route('admin.roles.update', $role), [
                    'name' => $coreRoleName.'-renamed',
                ])
                ->assertRedirect(route('admin.roles.index'))
                ->assertSessionHas('error');

            $this->assertDatabaseHas('roles', [
                'id' => $role->id,
                'name' => $coreRoleName,
            ]);

            $this->assertDatabaseMissing('roles', [
                'id' => $role->id,
                'name' => $coreRoleName.'-renamed',
            ]);
        }
    }

    public function test_admin_cannot_delete_core_roles(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        foreach (Role::coreRoles() as $coreRoleName) {
            $role = Role::query()->firstOrCreate(['name' => $coreRoleName]);

            $this->actingAs($admin)
                ->delete(route('admin.roles.destroy', $role))
                ->assertRedirect(route('admin.roles.index'))
                ->assertSessionHas('error');

            $this->assertDatabaseHas('roles', [
                'id' => $role->id,
                'name' => $coreRoleName,
            ]);
        }
    }

    public function test_admin_can_update_and_delete_non_core_role(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $role = Role::query()->create(['name' => 'manager']);
        $permission = Permission::query()->where('key', 'admin.products.view')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $role), [
                'name' => 'operator',
                'permissions' => [$permission->id],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'operator',
        ]);

        $this->assertTrue($role->fresh()->permissions()->whereKey($permission->id)->exists());

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $role->fresh()))
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_role_factory_generates_only_core_role_names(): void
    {
        $role = Role::factory()->make();

        $this->assertContains($role->name, Role::coreRoles());
    }

    public function test_admin_can_update_permissions_for_core_client_and_cook_roles(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $cook = Role::query()->where('name', Role::COOK)->firstOrFail();
        $permission = Permission::query()->where('key', 'cook.orders.view')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $cook), [
                'name' => Role::COOK,
                'permissions' => [$permission->id],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertSame(['cook.orders.view'], $cook->fresh()->permissions()->pluck('key')->all());
    }

    public function test_admin_roles_manage_permissions_is_required_to_sync_permissions(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $admin->role->permissions()->sync(
            Permission::query()
                ->whereIn('key', ['admin.roles.update', 'admin.roles.view'])
                ->pluck('id')
        );

        $role = Role::query()->create(['name' => 'limited']);
        $permission = Permission::query()->where('key', 'admin.users.view')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $role), [
                'name' => 'limited',
                'permissions' => [$permission->id],
            ])
            ->assertForbidden();

        $this->assertFalse($role->fresh()->permissions()->whereKey($permission->id)->exists());
    }

    public function test_user_without_permission_receives_403_on_permission_route(): void
    {
        Route::get('/permission-test', fn () => 'ok')
            ->middleware(['auth', 'permission:admin.roles.view']);

        $client = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($client)
            ->get('/permission-test')
            ->assertForbidden();
    }

    public function test_admin_role_keeps_all_permissions_when_update_posts_empty_permissions(): void
    {
        $adminUser = $this->createUserWithRole(Role::ADMIN);
        $adminRole = Role::query()->where('name', Role::ADMIN)->firstOrFail();

        $this->actingAs($adminUser)
            ->put(route('admin.roles.update', $adminRole), [
                'name' => Role::ADMIN,
                'permissions' => [],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertSame(Permission::query()->count(), $adminRole->fresh()->permissions()->count());
        $this->assertTrue($adminRole->fresh()->permissions()->where('key', 'admin.roles.manage_permissions')->exists());
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
