@extends('layouts.app')

@section('title', 'Editar rol')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Editar rol</h1>
            <p class="adminUsersSubtitle">Actualiza el nombre del rol seleccionado.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.roles.index') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
        </div>
    </header>

    @if (in_array($role->name, \App\Models\Role::coreRoles(), true))
        <div class="adminAlert adminAlertError">
            Este es un rol base del sistema. El controlador bloqueará cambios que rompan RBAC.
        </div>
    @endif

    <section class="adminUsersCard">
        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
            @method('PUT')

            @include('admin.roles._form', [
                'role' => $role,
                'buttonText' => 'Guardar cambios',
            ])
        </form>
    </section>
</div>
@endsection
