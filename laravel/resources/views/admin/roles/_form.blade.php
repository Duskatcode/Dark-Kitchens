@csrf

@php
    $selectedPermissions = collect(old('permissions', isset($role) && $role ? $role->permissions->pluck('id')->all() : []))
        ->map(fn ($permissionId) => (int) $permissionId)
        ->all();
    $isAdminRole = isset($role) && $role && $role->name === \App\Models\Role::ADMIN;
    $isCoreRole = isset($role) && $role && $role->isCoreRole();
    $canManagePermissions = $canManagePermissions ?? true;
    $permissionsLocked = $isAdminRole || ! $canManagePermissions;
@endphp

<div class="adminRoleForm">
    <section class="adminRoleFormCard adminRoleIdentityCard">
        <div class="adminRoleSectionHeader">
            <div>
                <h2>Datos del rol</h2>
                <p>Define el identificador que se usará para asignar accesos.</p>
            </div>
        </div>

        <div class="adminFormGroup">
            <label class="adminFormLabel" for="name">Nombre del rol</label>
            <input
                id="name"
                class="adminFormControl"
                type="text"
                name="name"
                value="{{ old('name', $role->name ?? '') }}"
                required
                maxlength="50"
                placeholder="Ej: manager"
                @if ($isCoreRole) readonly @endif
            >

            @error('name')
                <span class="adminFieldError">{{ $message }}</span>
            @enderror

            <p class="adminFormHint">
                Usa nombres en minúscula. Los roles base son admin, client y cook.
            </p>
        </div>
    </section>

    <div class="adminRoleAlerts">
        @if ($isCoreRole)
            <div class="adminRoleNotice adminRoleNoticeWarning">
                Rol base del sistema. El nombre no se puede modificar.
            </div>
        @endif
        @if ($isAdminRole)
            <div class="adminRoleNotice adminRoleNoticeInfo">
                Admin conserva siempre todos los permisos.
            </div>
        @elseif (! $canManagePermissions)
            <div class="adminRoleNotice adminRoleNoticeWarning">
                No tienes permiso para cambiar asignaciones de permisos.
            </div>
        @endif
    </div>

    <section class="adminRoleFormCard">
        <div class="adminRoleSectionHeader">
            <div>
                <h2>Permisos</h2>
                <p>Selecciona las acciones disponibles para este rol.</p>
            </div>
        </div>

        @error('permissions')
            <span class="adminFieldError">{{ $message }}</span>
        @enderror

        @error('permissions.*')
            <span class="adminFieldError">{{ $message }}</span>
        @enderror

        <div class="adminPermissionGroups">
            @forelse ($permissionsByGroup as $group => $permissions)
                <section class="adminPermissionGroupCard">
                    <header class="adminPermissionGroupHeader">
                        <div>
                            <h3>{{ $group }}</h3>
                            <span>{{ $permissions->count() }} permisos</span>
                        </div>
                    </header>

                    <div class="adminPermissionTileGrid">
                        @foreach ($permissions as $permission)
                            <label class="adminPermissionTile @if ($permissionsLocked) is-disabled @endif">
                                <span class="adminPermissionTileTop">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                        @checked($isAdminRole || in_array($permission->id, $selectedPermissions, true))
                                        @disabled($permissionsLocked)
                                    >
                                    <strong>{{ $permission->name }}</strong>
                                </span>

                                <code>{{ $permission->key }}</code>

                                <span class="adminPermissionTileDescription">
                                    @if ($permission->description)
                                        {{ $permission->description }}
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>
            @empty
                <p class="adminFormHint">No hay permisos registrados. Ejecuta los seeders para cargarlos.</p>
            @endforelse
        </div>
    </section>

    <div class="adminFormActions">
        <a href="{{ route('admin.roles.index') }}" class="adminUsersButton adminUsersButtonSecondary">Cancelar</a>
        <button type="submit" class="adminUsersButton adminUsersButtonPrimary">{{ $buttonText }}</button>
    </div>
</div>
