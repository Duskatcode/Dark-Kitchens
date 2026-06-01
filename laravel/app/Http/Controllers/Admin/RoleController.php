<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->with('permissions')
            ->withCount('permissions')
            ->orderBy('id')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissionsByGroup = $this->permissionsByGroup();
        $canManagePermissions = request()->user()?->hasPermission('admin.roles.manage_permissions') ?? false;

        return view('admin.roles.create', compact('canManagePermissions', 'permissionsByGroup'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'lowercase',
                'alpha_dash',
                Rule::unique('roles', 'name'),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'distinct', 'exists:permissions,id'],
        ]);

        if (! empty($validated['permissions'])) {
            $this->authorizePermissionManagement($request);
        }

        DB::transaction(function () use ($validated): void {
            $role = Role::query()->create([
                'name' => $validated['name'],
            ]);

            $role->permissions()->sync($validated['permissions'] ?? []);
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Rol creado correctamente.');
    }

    public function show(Role $role): RedirectResponse
    {
        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');
        $permissionsByGroup = $this->permissionsByGroup();
        $canManagePermissions = request()->user()?->hasPermission('admin.roles.manage_permissions') ?? false;

        return view('admin.roles.edit', compact('canManagePermissions', 'permissionsByGroup', 'role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'lowercase',
                'alpha_dash',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'distinct', 'exists:permissions,id'],
        ]);

        if ($role->isCoreRole() && $validated['name'] !== $role->name) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'El rol "'.$role->name.'" no se puede renombrar.');
        }

        if ($this->permissionsChanged($role, $validated['permissions'] ?? [])) {
            $this->authorizePermissionManagement($request);
        }

        DB::transaction(function () use ($role, $validated): void {
            if (! $role->isCoreRole()) {
                $role->update([
                    'name' => $validated['name'],
                ]);
            }

            if ($role->name === Role::ADMIN) {
                $role->permissions()->sync(Permission::query()->pluck('id'));
            } else {
                $role->permissions()->sync($validated['permissions'] ?? []);
            }
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->isCoreRole()) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'El rol "'.$role->name.'" no se puede eliminar.');
        }

        if ($role->users()->exists()) {
            return redirect()
                ->route('admin.roles.index')
                ->withErrors([
                    'delete' => 'No puedes eliminar un rol con usuarios asociados.',
                ]);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Rol eliminado correctamente.');
    }

    /**
     * @return Collection<string, Collection<int, Permission>>
     */
    private function permissionsByGroup(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->sortBy(fn (Permission $permission): string => str_pad((string) $this->groupPosition($permission->group), 2, '0', STR_PAD_LEFT).$permission->name)
            ->groupBy(fn (Permission $permission): string => $permission->group ?: 'General');
    }

    private function groupPosition(?string $group): int
    {
        return match ($group) {
            'Sistema' => 0,
            'Accesos y seguridad' => 1,
            'Catálogo' => 2,
            'Pedidos' => 3,
            'Cocina' => 4,
            'Cliente' => 5,
            'Reportes' => 6,
            default => 99,
        };
    }

    /**
     * @param  array<int, int|string>  $submittedPermissionIds
     */
    private function permissionsChanged(Role $role, array $submittedPermissionIds): bool
    {
        if ($role->name === Role::ADMIN) {
            $submittedPermissionIds = Permission::query()->pluck('id')->all();
        }

        $currentPermissionIds = $role->permissions()
            ->pluck('permissions.id')
            ->map(fn (int $permissionId): int => $permissionId)
            ->sort()
            ->values()
            ->all();

        $submittedPermissionIds = collect($submittedPermissionIds)
            ->map(fn (int|string $permissionId): int => (int) $permissionId)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $currentPermissionIds !== $submittedPermissionIds;
    }

    private function authorizePermissionManagement(Request $request): void
    {
        if (! $request->user()?->hasPermission('admin.roles.manage_permissions')) {
            abort(403, 'No tienes permiso para gestionar permisos de roles.');
        }
    }
}
