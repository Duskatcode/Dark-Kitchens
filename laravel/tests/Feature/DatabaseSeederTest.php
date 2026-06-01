<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_base_roles_users_statuses_categories_and_products(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (Role::coreRoles() as $roleName) {
            $this->assertDatabaseHas('roles', [
                'name' => $roleName,
            ]);
        }

        foreach (['pending', 'in_progress', 'completed', 'cancelled'] as $statusName) {
            $this->assertDatabaseHas('statuses', [
                'name' => $statusName,
            ]);
        }

        foreach (['admin@test.com', 'client@test.com', 'cook@test.com'] as $email) {
            $this->assertDatabaseHas('users', [
                'email' => $email,
            ]);
        }

        $this->assertGreaterThanOrEqual(4, Category::query()->count());
        $this->assertGreaterThanOrEqual(5, Product::query()->count());

        $this->assertTrue(Product::query()->where('is_available', true)->exists());
        $this->assertTrue(Product::query()->whereHas('category')->exists());
        $this->assertSame(3, User::query()->count());
        $this->assertSame(3, Role::query()->count());
        $this->assertSame(4, Status::query()->count());
        $this->assertGreaterThanOrEqual(20, Permission::query()->count());
        $this->assertSame(Permission::query()->count(), Role::query()->where('name', Role::ADMIN)->firstOrFail()->permissions()->count());
    }

    public function test_permission_seeder_creates_expected_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach ([
            'admin.dashboard.view',
            'admin.roles.manage_permissions',
            'admin.orders.update_status',
            'cook.orders.update_status',
            'client.orders.cancel',
            'admin.reports.export',
        ] as $permissionKey) {
            $this->assertDatabaseHas('permissions', [
                'key' => $permissionKey,
            ]);
        }

        $this->assertDatabaseMissing('permissions', [
            'key' => 'cook.orders.update',
        ]);
    }

    public function test_role_seeder_assigns_expected_base_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = Role::query()->where('name', Role::ADMIN)->firstOrFail();
        $cook = Role::query()->where('name', Role::COOK)->firstOrFail();
        $client = Role::query()->where('name', Role::CLIENT)->firstOrFail();

        $this->assertSame(Permission::query()->count(), $admin->permissions()->count());
        $this->assertEqualsCanonicalizing(
            ['cook.orders.view', 'cook.orders.update_status'],
            $cook->permissions()->pluck('key')->all()
        );
        $this->assertEqualsCanonicalizing(
            ['client.orders.view', 'client.orders.create', 'client.orders.cancel'],
            $client->permissions()->pluck('key')->all()
        );
    }

    public function test_database_seeder_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, Role::query()->count());
        $this->assertSame(4, Status::query()->count());
        $this->assertSame(3, User::query()->count());
        $this->assertSame(4, Category::query()->count());
        $this->assertSame(5, Product::query()->count());
        $this->assertGreaterThanOrEqual(20, Permission::query()->count());
    }
}
