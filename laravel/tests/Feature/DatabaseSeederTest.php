<?php

namespace Tests\Feature;

use App\Models\Category;
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
    }
}
