<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('role')
            ->latest('id')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::query()->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function show(User $user): View
    {
        $user->load('role');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $user->load('role');
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $newRoleId = (int) $validated['role_id'];

        if ($this->isSelfDemotion($request->user(), $user, $newRoleId)) {
            return back()
                ->withErrors([
                    'role_id' => 'No puedes quitarte el rol de administrador.',
                ])
                ->withInput();
        }

        if ($this->isRemovingLastAdmin($user, $newRoleId)) {
            return back()
                ->withErrors([
                    'role_id' => 'Debe existir al menos un administrador activo en el sistema.',
                ])
                ->withInput();
        }

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $authUser = request()->user();

        if ($authUser && $authUser->is($user)) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors([
                    'delete' => 'No puedes eliminar tu propio usuario administrador.',
                ]);
        }

        if ($this->isRemovingLastAdmin($user, null)) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors([
                    'delete' => 'No puedes eliminar al último administrador del sistema.',
                ]);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario eliminado correctamente.');
    }

    private function isRemovingLastAdmin(User $user, ?int $newRoleId): bool
    {
        $isAdminUser = $user->isAdmin();

        if (! $isAdminUser) {
            return false;
        }

        if ($newRoleId !== null && $user->role_id === $newRoleId) {
            return false;
        }

        if ($newRoleId !== null && $newRoleId === $this->adminRoleId()) {
            return false;
        }

        return $this->adminUsersCount() <= 1;
    }

    private function isSelfDemotion(?User $authUser, User $targetUser, int $newRoleId): bool
    {
        if (! $authUser) {
            return false;
        }

        if (! $authUser->is($targetUser)) {
            return false;
        }

        if (! $authUser->isAdmin()) {
            return false;
        }

        return $authUser->role_id !== $newRoleId;
    }

    private function adminRoleId(): int
    {
        return (int) Role::query()->where('name', 'Admin')->value('id');
    }

    private function adminUsersCount(): int
    {
        return User::query()
            ->whereHas('role', function ($query) {
                $query->where('name', 'Admin');
            })
            ->count();
    }
}
