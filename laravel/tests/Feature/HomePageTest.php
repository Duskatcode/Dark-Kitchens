<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_can_see_business_landing_page(): void
    {
        $this->get(route('home.index'))
            ->assertOk()
            ->assertSee('Dark Kitchens')
            ->assertSee('Organiza pedidos, menú y tareas de cocina')
            ->assertSee('Iniciar sesión')
            ->assertSee('Crear cuenta');
    }

    public function test_authenticated_user_sees_dashboard_action_on_home_page(): void
    {
        $user = $this->createUserWithRole(Role::CLIENT);

        $this->actingAs($user)
            ->get(route('home.index'))
            ->assertOk()
            ->assertSee('Ir al panel')
            ->assertDontSee('Crear cuenta');
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
