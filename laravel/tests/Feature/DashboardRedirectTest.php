<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_client_is_redirected_to_client_dashboard(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($client)
            ->get(route('dashboard'))
            ->assertRedirect(route('client.dashboard'));
    }

    public function test_cook_is_redirected_to_cook_dashboard(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);

        $this->actingAs($cook)
            ->get(route('dashboard'))
            ->assertRedirect(route('cook.dashboard'));
    }

    public function test_admin_dashboard_requires_admin_role(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($client)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_client_dashboard_requires_client_role(): void
    {
        $cook = $this->createUserWithRole(Role::COOK);

        $this->actingAs($cook)
            ->get(route('client.dashboard'))
            ->assertForbidden();
    }

    public function test_cook_dashboard_requires_cook_role(): void
    {
        $client = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($client)
            ->get(route('cook.dashboard'))
            ->assertForbidden();
    }

    public function test_role_dashboards_render_for_correct_roles(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN);
        $client = $this->createUserWithRole(Role::CLIENT);
        $cook = $this->createUserWithRole(Role::COOK);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Panel administrativo');

        $this->actingAs($client)
            ->get(route('client.dashboard'))
            ->assertOk()
            ->assertSee('Área cliente');

        $this->actingAs($cook)
            ->get(route('cook.dashboard'))
            ->assertOk()
            ->assertSee('Panel de cocina');
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
