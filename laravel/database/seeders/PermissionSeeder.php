<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * @return array<int, array{key: string, name: string, group: string, description: string}>
     */
    public static function permissions(): array
    {
        return [
            ['key' => 'admin.dashboard.view', 'name' => 'Ver panel administrativo', 'group' => 'Sistema', 'description' => 'Permite acceder al dashboard administrativo.'],
            ['key' => 'admin.users.view', 'name' => 'Ver usuarios', 'group' => 'Accesos y seguridad', 'description' => 'Permite listar y consultar usuarios.'],
            ['key' => 'admin.users.create', 'name' => 'Crear usuarios', 'group' => 'Accesos y seguridad', 'description' => 'Permite registrar usuarios desde administración.'],
            ['key' => 'admin.users.update', 'name' => 'Editar usuarios', 'group' => 'Accesos y seguridad', 'description' => 'Permite modificar datos y roles de usuarios.'],
            ['key' => 'admin.users.delete', 'name' => 'Eliminar usuarios', 'group' => 'Accesos y seguridad', 'description' => 'Permite eliminar usuarios permitidos.'],
            ['key' => 'admin.roles.view', 'name' => 'Ver roles', 'group' => 'Accesos y seguridad', 'description' => 'Permite listar roles y permisos asignados.'],
            ['key' => 'admin.roles.create', 'name' => 'Crear roles', 'group' => 'Accesos y seguridad', 'description' => 'Permite crear roles personalizados.'],
            ['key' => 'admin.roles.update', 'name' => 'Editar roles', 'group' => 'Accesos y seguridad', 'description' => 'Permite cambiar el nombre de roles no base.'],
            ['key' => 'admin.roles.delete', 'name' => 'Eliminar roles', 'group' => 'Accesos y seguridad', 'description' => 'Permite eliminar roles personalizados sin usuarios.'],
            ['key' => 'admin.roles.manage_permissions', 'name' => 'Gestionar permisos de roles', 'group' => 'Accesos y seguridad', 'description' => 'Permite asignar o retirar permisos a roles.'],
            ['key' => 'admin.categories.view', 'name' => 'Ver categorías', 'group' => 'Catálogo', 'description' => 'Permite listar categorías del menú.'],
            ['key' => 'admin.categories.create', 'name' => 'Crear categorías', 'group' => 'Catálogo', 'description' => 'Permite crear categorías del menú.'],
            ['key' => 'admin.categories.update', 'name' => 'Editar categorías', 'group' => 'Catálogo', 'description' => 'Permite modificar categorías del menú.'],
            ['key' => 'admin.categories.delete', 'name' => 'Eliminar categorías', 'group' => 'Catálogo', 'description' => 'Permite eliminar categorías sin productos asociados.'],
            ['key' => 'admin.products.view', 'name' => 'Ver productos', 'group' => 'Catálogo', 'description' => 'Permite listar y consultar productos.'],
            ['key' => 'admin.products.create', 'name' => 'Crear productos', 'group' => 'Catálogo', 'description' => 'Permite crear productos del menú.'],
            ['key' => 'admin.products.update', 'name' => 'Editar productos', 'group' => 'Catálogo', 'description' => 'Permite modificar productos del menú.'],
            ['key' => 'admin.products.delete', 'name' => 'Eliminar productos', 'group' => 'Catálogo', 'description' => 'Permite eliminar productos permitidos.'],
            ['key' => 'admin.orders.view', 'name' => 'Ver pedidos', 'group' => 'Pedidos', 'description' => 'Permite consultar pedidos desde administración.'],
            ['key' => 'admin.orders.update_status', 'name' => 'Actualizar estado de pedidos', 'group' => 'Pedidos', 'description' => 'Permite cambiar el estado de pedidos.'],
            ['key' => 'admin.orders.delete', 'name' => 'Eliminar pedidos', 'group' => 'Pedidos', 'description' => 'Permite eliminar pedidos permitidos.'],
            ['key' => 'cook.orders.view', 'name' => 'Ver pedidos de cocina', 'group' => 'Cocina', 'description' => 'Permite consultar la cola de cocina.'],
            ['key' => 'cook.orders.update_status', 'name' => 'Actualizar estado en cocina', 'group' => 'Cocina', 'description' => 'Permite avanzar pedidos desde cocina.'],
            ['key' => 'client.orders.view', 'name' => 'Ver pedidos propios', 'group' => 'Cliente', 'description' => 'Permite consultar pedidos propios.'],
            ['key' => 'client.orders.create', 'name' => 'Crear pedidos', 'group' => 'Cliente', 'description' => 'Permite crear pedidos como cliente.'],
            ['key' => 'client.orders.cancel', 'name' => 'Cancelar pedidos propios', 'group' => 'Cliente', 'description' => 'Permite cancelar pedidos propios elegibles.'],
            ['key' => 'admin.reports.view', 'name' => 'Ver reportes', 'group' => 'Reportes', 'description' => 'Permite consultar reportes administrativos.'],
            ['key' => 'admin.reports.export', 'name' => 'Exportar reportes', 'group' => 'Reportes', 'description' => 'Permite exportar reportes administrativos.'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function cookPermissionKeys(): array
    {
        return [
            'cook.orders.view',
            'cook.orders.update_status',
        ];
    }

    /**
     * @return list<string>
     */
    public static function clientPermissionKeys(): array
    {
        return [
            'client.orders.view',
            'client.orders.create',
            'client.orders.cancel',
        ];
    }

    public function run(): void
    {
        $this->normalizeLegacyPermissions();

        foreach (self::permissions() as $permission) {
            Permission::query()->updateOrCreate(
                ['key' => $permission['key']],
                [
                    'name' => $permission['name'],
                    'group' => $permission['group'],
                    'description' => $permission['description'],
                ]
            );
        }
    }

    public static function syncBaseRolePermissions(): void
    {
        $admin = Role::query()->where('name', Role::ADMIN)->first();
        $client = Role::query()->where('name', Role::CLIENT)->first();
        $cook = Role::query()->where('name', Role::COOK)->first();

        if ($admin) {
            $admin->permissions()->sync(Permission::query()->pluck('id'));
        }

        if ($client) {
            $client->permissions()->sync(
                Permission::query()->whereIn('key', self::clientPermissionKeys())->pluck('id')
            );
        }

        if ($cook) {
            $cook->permissions()->sync(
                Permission::query()->whereIn('key', self::cookPermissionKeys())->pluck('id')
            );
        }
    }

    private function normalizeLegacyPermissions(): void
    {
        $legacy = Permission::query()->where('key', 'cook.orders.update')->first();

        if (! $legacy) {
            return;
        }

        $replacement = Permission::query()->where('key', 'cook.orders.update_status')->first();

        if (! $replacement) {
            $legacy->update(['key' => 'cook.orders.update_status']);

            return;
        }

        $replacement->roles()->syncWithoutDetaching($legacy->roles()->pluck('roles.id'));
        $legacy->delete();
    }
}
