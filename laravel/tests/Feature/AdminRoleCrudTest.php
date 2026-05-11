<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
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

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name' => 'manager',
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'name' => 'manager',
        ]);
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

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $role), [
                'name' => 'operator',
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'operator',
        ]);

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

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
