<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_cannot_access_client_orders(): void
    {
        $this->get(route('client.orders.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_client_orders(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->get(route('client.orders.index'))
            ->assertForbidden();
    }

    public function test_cook_cannot_access_client_orders(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);

        $this->actingAs($cook)
            ->get(route('client.orders.index'))
            ->assertForbidden();
    }

    public function test_client_can_create_order_from_available_product(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);
        $category = Category::query()->create(['name' => 'Platos fuertes']);
        Status::query()->firstOrCreate(['name' => 'pending']);

        $product = Product::query()->create([
            'name' => 'Burger clásica',
            'description' => 'Hamburguesa artesanal.',
            'price' => 24000,
            'is_available' => true,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($client)
            ->post(route('client.orders.store'), [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        $order = Order::query()->with(['status', 'orderDetails'])->firstOrFail();

        $response->assertRedirect(route('client.orders.show', $order));

        $this->assertSame($client->id, $order->user_id);
        $this->assertSame('pending', $order->status->name);
        $this->assertSame('48000.00', (string) $order->total_amount);

        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 24000,
            'subtotal' => 48000,
        ]);
    }

    public function test_order_price_is_taken_from_database(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);
        $category = Category::query()->create(['name' => 'Bebidas']);

        $product = Product::query()->create([
            'name' => 'Limonada',
            'description' => 'Limonada natural.',
            'price' => 7000,
            'is_available' => true,
            'category_id' => $category->id,
        ]);

        $this->actingAs($client)
            ->post(route('client.orders.store'), [
                'product_id' => $product->id,
                'quantity' => 3,
                'price' => 1,
                'subtotal' => 1,
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $client->id,
            'total_amount' => 21000,
        ]);

        $this->assertDatabaseHas('order_details', [
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 7000,
            'subtotal' => 21000,
        ]);
    }

    public function test_client_cannot_order_unavailable_product(): void
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
            ->post(route('client.orders.store'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_details', 0);
    }

    public function test_quantity_must_be_at_least_one(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);
        $category = Category::query()->create(['name' => 'Entradas']);

        $product = Product::query()->create([
            'name' => 'Papas',
            'description' => 'Papas rústicas.',
            'price' => 12000,
            'is_available' => true,
            'category_id' => $category->id,
        ]);

        $this->actingAs($client)
            ->from(route('client.menu.show', $product))
            ->post(route('client.orders.store'), [
                'product_id' => $product->id,
                'quantity' => 0,
            ])
            ->assertRedirect(route('client.menu.show', $product))
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_client_can_only_see_own_orders(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);
        $otherClient = $this->createUserWithRole(Role::CLIENT);
        $status = Status::query()->firstOrCreate(['name' => 'pending']);

        $ownOrder = Order::query()->create([
            'user_id' => $client->id,
            'order_date' => now(),
            'total_amount' => 10000,
            'status_id' => $status->id,
        ]);

        $otherOrder = Order::query()->create([
            'user_id' => $otherClient->id,
            'order_date' => now(),
            'total_amount' => 20000,
            'status_id' => $status->id,
        ]);

        $this->actingAs($client)
            ->get(route('client.orders.index'))
            ->assertOk()
            ->assertSee('Pedido #'.$ownOrder->id)
            ->assertDontSee('Pedido #'.$otherOrder->id);

        $this->actingAs($client)
            ->get(route('client.orders.show', $otherOrder))
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
