@extends('layouts.app')

@section('title', 'Nuevo rol')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
    @vite('resources/css/pages/admin-roles.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Nuevo rol</h1>
            <p class="adminUsersSubtitle">Crea un rol adicional para extender permisos del sistema.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.roles.index') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
        </div>
    </header>

    <section class="adminUsersCard">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @include('admin.roles._form', [
                'role' => null,
                'permissionsByGroup' => $permissionsByGroup,
                'canManagePermissions' => $canManagePermissions,
                'buttonText' => 'Crear rol',
            ])
        </form>
    </section>
</div>
@endsection
