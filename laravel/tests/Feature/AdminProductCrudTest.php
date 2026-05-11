<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_cannot_access_admin_products(): void
    {
        $this->get(route('admin.products.index'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_admin_products(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($client)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_view_update_and_delete_product(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $category = Category::query()->create(['name' => 'Platos fuertes']);
        $newCategory = Category::query()->create(['name' => 'Combos']);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Burger clásica',
                'description' => 'Hamburguesa artesanal con queso.',
                'price' => 24000,
                'is_available' => 1,
                'category_id' => $category->id,
            ])
            ->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('name', 'Burger clásica')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.products.show', $product))
            ->assertOk()
            ->assertSee('Burger clásica');

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), [
                'name' => 'Burger doble',
                'description' => 'Hamburguesa artesanal doble con queso.',
                'price' => 29000,
                'is_available' => 0,
                'category_id' => $newCategory->id,
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Burger doble',
            'price' => 29000,
            'is_available' => false,
            'category_id' => $newCategory->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product->fresh()))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_admin_cannot_delete_product_associated_to_orders(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $client = $this->createUserWithRole(Role::CLIENT);
        $category = Category::query()->create(['name' => 'Bebidas']);
        $status = Status::query()->firstOrCreate(['name' => 'pending']);

        $product = Product::query()->create([
            'name' => 'Limonada',
            'description' => 'Limonada natural.',
            'price' => 7000,
            'is_available' => true,
            'category_id' => $category->id,
        ]);

        $order = Order::query()->create([
            'user_id' => $client->id,
            'order_date' => now(),
            'total_amount' => 7000,
            'status_id' => $status->id,
        ]);

        OrderDetail::query()->create([
            'product_id' => $product->id,
            'order_id' => $order->id,
            'quantity' => 1,
            'unit_price' => 7000,
            'subtotal' => 7000,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_validation_requires_existing_category(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->from(route('admin.products.create'))
            ->post(route('admin.products.store'), [
                'name' => 'Producto inválido',
                'description' => 'Sin categoría válida.',
                'price' => 10000,
                'is_available' => 1,
                'category_id' => 999,
            ])
            ->assertRedirect(route('admin.products.create'))
            ->assertSessionHasErrors('category_id');
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
