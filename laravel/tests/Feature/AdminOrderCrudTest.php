<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    }

    public function test_guest_cannot_access_admin_orders(): void
    {
        $this->get(route('admin.orders.index'))
            ->assertRedirect(route('login'));
    }

    public function test_client_cannot_access_admin_orders(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($client)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }

    public function test_cook_cannot_access_admin_orders(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);

        $this->actingAs($cook)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_all_orders(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $pendingOrder = $this->createOrderWithStatus('pending');
        $completedOrder = $this->createOrderWithStatus('completed');

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Pedido #'.$pendingOrder->id)
            ->assertSee('Pedido #'.$completedOrder->id);
    }

    public function test_admin_can_view_order_detail(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $order = $this->createOrderWithStatus('pending');

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Pedido #'.$order->id)
            ->assertSee('Burger clásica')
            ->assertSee($order->user->email);
    }

    public function test_admin_can_filter_orders_by_status(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $pendingOrder = $this->createOrderWithStatus('pending');
        $completedOrder = $this->createOrderWithStatus('completed');

        $this->actingAs($admin)
            ->get(route('admin.orders.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('Pedido #'.$pendingOrder->id)
            ->assertDontSee('Pedido #'.$completedOrder->id);
    }

    public function test_admin_can_update_order_status_with_valid_transition(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $order = $this->createOrderWithStatus('pending');
        $nextStatus = Status::query()->firstOrCreate(['name' => 'in_progress']);

        $this->actingAs($admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status_id' => $nextStatus->id,
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertSame('in_progress', $order->fresh()->status->name);
    }

    public function test_admin_cannot_update_order_status_with_invalid_transition(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $order = $this->createOrderWithStatus('completed');
        $nextStatus = Status::query()->firstOrCreate(['name' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status_id' => $nextStatus->id,
            ])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHasErrors('status_id');

        $this->assertSame('completed', $order->fresh()->status->name);
    }

    public function test_admin_orders_require_view_permission(): void
    {
        $admin = $this->createAdminWithoutPermission('admin.orders.view');
        $order = $this->createOrderWithStatus('pending');

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertForbidden();
    }

    public function test_admin_orders_require_update_status_permission(): void
    {
        $admin = $this->createAdminWithoutPermission('admin.orders.update_status');
        $order = $this->createOrderWithStatus('pending');
        $nextStatus = Status::query()->firstOrCreate(['name' => 'in_progress']);

        $this->actingAs($admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status_id' => $nextStatus->id,
            ])
            ->assertForbidden();

        $this->assertSame('pending', $order->fresh()->status->name);
    }

    public function test_admin_orders_require_delete_permission(): void
    {
        $admin = $this->createAdminWithoutPermission('admin.orders.delete');
        $order = $this->createOrderWithStatus('pending');

        $this->actingAs($admin)
            ->delete(route('admin.orders.destroy', $order))
            ->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
    }

    public function test_admin_can_delete_pending_order(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $order = $this->createOrderWithStatus('pending');

        $this->actingAs($admin)
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }

    public function test_admin_cannot_delete_non_pending_order(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $order = $this->createOrderWithStatus('completed');

        $this->actingAs($admin)
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    private function createAdminWithoutPermission(string $permissionKey): User
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $permissionIds = Permission::query()
            ->where('key', '!=', $permissionKey)
            ->pluck('id')
            ->all();

        $admin->role->permissions()->sync($permissionIds);

        return $admin;
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

        return $order->load(['user', 'status', 'orderDetails.product.category']);
    }
}
