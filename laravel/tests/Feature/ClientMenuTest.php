<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_cannot_access_client_menu(): void
    {
        $this->get(route('client.menu.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_client_menu(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->get(route('client.menu.index'))
            ->assertForbidden();
    }

    public function test_cook_cannot_access_client_menu(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);

        $this->actingAs($cook)
            ->get(route('client.menu.index'))
            ->assertForbidden();
    }

    public function test_client_can_see_only_available_products(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);
        $category = Category::query()->create(['name' => 'Platos fuertes']);

        Product::query()->create([
            'name' => 'Burger disponible',
            'description' => 'Producto visible.',
            'price' => 24000,
            'is_available' => true,
            'category_id' => $category->id,
        ]);

        Product::query()->create([
            'name' => 'Burger oculta',
            'description' => 'Producto no visible.',
            'price' => 26000,
            'is_available' => false,
            'category_id' => $category->id,
        ]);

        $this->actingAs($client)
            ->get(route('client.menu.index'))
            ->assertOk()
            ->assertSee('Burger disponible')
            ->assertDontSee('Burger oculta');
    }

    public function test_client_can_filter_menu_by_category(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);
        $burgers = Category::query()->create(['name' => 'Hamburguesas']);
        $drinks = Category::query()->create(['name' => 'Bebidas']);

        Product::query()->create([
            'name' => 'Burger clásica',
            'description' => 'Hamburguesa visible.',
            'price' => 24000,
            'is_available' => true,
            'category_id' => $burgers->id,
        ]);

        Product::query()->create([
            'name' => 'Limonada',
            'description' => 'Bebida visible.',
            'price' => 7000,
            'is_available' => true,
            'category_id' => $drinks->id,
        ]);

        $this->actingAs($client)
            ->get(route('client.menu.index', ['category_id' => $drinks->id]))
            ->assertOk()
            ->assertSee('Limonada')
            ->assertDontSee('Burger clásica');
    }

    public function test_client_can_see_available_product_detail(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);
        $category = Category::query()->create(['name' => 'Postres']);

        $product = Product::query()->create([
            'name' => 'Brownie',
            'description' => 'Brownie de chocolate.',
            'price' => 9000,
            'is_available' => true,
            'category_id' => $category->id,
        ]);

        $this->actingAs($client)
            ->get(route('client.menu.show', $product))
            ->assertOk()
            ->assertSee('Brownie')
            ->assertSee('Brownie de chocolate.');
    }

    public function test_unavailable_product_detail_returns_not_found(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);
        $category = Category::query()->create(['name' => 'Postres']);

        $product = Product::query()->create([
            'name' => 'Postre oculto',
            'description' => 'No disponible.',
            'price' => 9000,
            'is_available' => false,
            'category_id' => $category->id,
        ]);

        $this->actingAs($client)
            ->get(route('client.menu.show', $product))
            ->assertNotFound();
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
