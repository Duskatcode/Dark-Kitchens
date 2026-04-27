<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::orderBy('id')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'max:50', 'unique:roles,name'],
        ]);

        Role::create($validated);

        session()->flash('success', 'Role created successfully.');

        return redirect()->route('admin.roles.index');
    }

    public function show(Role $role): RedirectResponse
    {
        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'max:50',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
        ]);

        $role->update($validated);

        session()->flash('success', 'Role updated successfully.');

        return redirect()->route('admin.roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $protectedRoles = Role::coreRoles();

        if (in_array(strtolower($role->name), $protectedRoles, true)) {
            session()->flash('error', 'The role "'.$role->name.'" cannot be deleted.');

            return redirect()->route('admin.roles.index');
        }

        $role->delete();

        session()->flash('success', 'Role deleted successfully.');

        return redirect()->route('admin.roles.index');
    }
}
