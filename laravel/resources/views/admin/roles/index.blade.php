@extends('layouts.app')

@section('title', 'Roles')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
    @vite('resources/css/pages/admin-roles.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Roles</h1>
            <p class="adminUsersSubtitle">Gestiona los roles disponibles para controlar accesos dentro de Dark Kitchens.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.dashboard') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
            <a href="{{ route('admin.roles.create') }}" class="adminUsersButton adminUsersButtonPrimary">Nuevo rol</a>
        </div>
    </header>

    @if (session('status'))
        <div class="adminAlert adminAlertSuccess">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="adminAlert adminAlertError">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="adminAlert adminAlertError">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="adminUsersCard">
        <table class="adminUsersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Permisos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    @php
                        $isCoreRole = in_array($role->name, \App\Models\Role::coreRoles(), true);
                    @endphp

                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            <span class="adminBadge {{ $isCoreRole ? 'adminBadgeInfo' : 'adminBadgeNeutral' }}">
                                {{ $isCoreRole ? 'Core' : 'Personalizado' }}
                            </span>
                        </td>
                        <td>
                            <div class="adminRolePermissions">
                                <span class="adminBadge adminBadgeNeutral">{{ $role->permissions_count }} permisos</span>

                                @foreach ($role->permissions->take(3) as $permission)
                                    <span class="adminPermissionBadge">{{ $permission->key }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div class="adminUsersTableActions">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="adminInlineAction">Editar</a>

                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('¿Eliminar este rol?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="adminInlineAction adminInlineActionDanger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No hay roles registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
