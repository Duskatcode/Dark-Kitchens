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

class CookOrderQueueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_cannot_access_cook_orders(): void
    {
        $this->get(route('cook.orders.index'))
            ->assertRedirect(route('login'));
    }

    public function test_client_cannot_access_cook_orders(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($client)
            ->get(route('cook.orders.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_cook_orders(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->get(route('cook.orders.index'))
            ->assertForbidden();
    }

    public function test_cook_can_see_pending_and_in_progress_orders_only(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);

        $pendingOrder = $this->createOrderWithStatus('pending');
        $inProgressOrder = $this->createOrderWithStatus('in_progress');
        $completedOrder = $this->createOrderWithStatus('completed');

        $this->actingAs($cook)
            ->get(route('cook.orders.index'))
            ->assertOk()
            ->assertSee('Pedido #'.$pendingOrder->id)
            ->assertSee('Pedido #'.$inProgressOrder->id)
            ->assertDontSee('Pedido #'.$completedOrder->id);
    }

    public function test_cook_can_see_order_detail(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);
        $order = $this->createOrderWithStatus('pending');

        $this->actingAs($cook)
            ->get(route('cook.orders.show', $order))
            ->assertOk()
            ->assertSee('Pedido #'.$order->id)
            ->assertSee('Burger clásica');
    }

    public function test_cook_can_move_pending_order_to_in_progress(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);
        $order = $this->createOrderWithStatus('pending');

        $this->actingAs($cook)
            ->patch(route('cook.orders.start', $order))
            ->assertRedirect(route('cook.orders.show', $order));

        $this->assertSame('in_progress', $order->fresh()->status->name);
    }

    public function test_cook_can_move_in_progress_order_to_completed(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);
        $order = $this->createOrderWithStatus('in_progress');

        $this->actingAs($cook)
            ->patch(route('cook.orders.complete', $order))
            ->assertRedirect(route('cook.orders.index'));

        $this->assertSame('completed', $order->fresh()->status->name);
    }

    public function test_cook_cannot_start_order_that_is_not_pending(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);
        $order = $this->createOrderWithStatus('completed');

        $this->actingAs($cook)
            ->patch(route('cook.orders.start', $order))
            ->assertRedirect(route('cook.orders.show', $order))
            ->assertSessionHasErrors('status');

        $this->assertSame('completed', $order->fresh()->status->name);
    }

    public function test_cook_cannot_complete_order_that_is_not_in_progress(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);
        $order = $this->createOrderWithStatus('pending');

        $this->actingAs($cook)
            ->patch(route('cook.orders.complete', $order))
            ->assertRedirect(route('cook.orders.show', $order))
            ->assertSessionHasErrors('status');

        $this->assertSame('pending', $order->fresh()->status->name);
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    private function createOrderWithStatus(string $statusName): Order
    {
        $client = $this->createUserWithRole(Role::CLIENT);
        $category = Category::query()->firstOrCreate(['name' => 'Platos fuertes']);

        $product = Product::query()->firstOrCreate(
            ['name' => 'Burger clásica'],
            [
                'description' => 'Hamburguesa artesanal.',
                'price' => 24000,
                'is_available' => true,
                'category_id' => $category->id,
            ]
        );

        $status = Status::query()->firstOrCreate(['name' => $statusName]);

        $order = Order::query()->create([
            'user_id' => $client->id,
            'order_date' => now(),
            'total_amount' => 24000,
            'status_id' => $status->id,
        ]);

        OrderDetail::query()->create([
            'product_id' => $product->id,
            'order_id' => $order->id,
            'quantity' => 1,
            'unit_price' => 24000,
            'subtotal' => 24000,
        ]);

        return $order;
    }
}
