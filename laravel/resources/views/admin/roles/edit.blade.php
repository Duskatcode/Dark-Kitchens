@extends('layouts.app')

@section('title', 'Editar rol')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
    @vite('resources/css/pages/admin-roles.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Editar rol</h1>
            <p class="adminUsersSubtitle">Actualiza el nombre y permisos del rol seleccionado.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.roles.index') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
        </div>
    </header>

    <section class="adminUsersCard">
        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
            @method('PUT')

            @include('admin.roles._form', [
                'role' => $role,
                'permissionsByGroup' => $permissionsByGroup,
                'canManagePermissions' => $canManagePermissions,
                'buttonText' => 'Guardar cambios',
            ])
        </form>
    </section>
</div>
@endsection
