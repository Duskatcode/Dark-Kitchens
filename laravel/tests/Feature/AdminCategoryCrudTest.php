<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    }

    public function test_guest_cannot_access_admin_categories(): void
    {
        $this->get(route('admin.categories.index'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_admin_categories(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($client)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_category(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->get(route('admin.categories.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Combos',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::query()->where('name', 'Combos')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Combos familiares',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Combos familiares',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category->fresh()))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $category = Category::query()->create(['name' => 'Bebidas']);

        Product::query()->create([
            'name' => 'Agua',
            'description' => 'Agua sin gas.',
            'price' => 4000,
            'is_available' => true,
            'category_id' => $category->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_category_name_is_required(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), [
                'name' => '',
            ])
            ->assertRedirect(route('admin.categories.create'))
            ->assertSessionHasErrors('name');
    }

    public function test_admin_category_actions_require_specific_permissions(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $category = Category::query()->create(['name' => 'Combos']);

        $admin->role->permissions()->sync([]);

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Sin permiso',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Sin permiso',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
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
