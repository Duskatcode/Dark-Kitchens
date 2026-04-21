@extends('layouts.app')

@section('title', 'Detalle de usuario')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Detalle de usuario #{{ $user->id }}</h1>
            <p class="adminUsersSubtitle">Información del usuario seleccionado.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.users.edit', $user) }}" class="adminUsersButton adminUsersButtonPrimary">Editar</a>
            <a href="{{ route('admin.users.index') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
        </div>
    </header>

    <section class="adminUsersCard adminUserDetailGrid">
        <div>
            <h2 class="adminDetailLabel">Nombre</h2>
            <p class="adminDetailValue">{{ $user->name }} {{ $user->last_name }}</p>
        </div>

        <div>
            <h2 class="adminDetailLabel">Correo</h2>
            <p class="adminDetailValue">{{ $user->email }}</p>
        </div>

        <div>
            <h2 class="adminDetailLabel">Rol</h2>
            <p class="adminDetailValue">{{ $user->role?->name ?? 'Sin rol' }}</p>
        </div>

        <div>
            <h2 class="adminDetailLabel">Creado</h2>
            <p class="adminDetailValue">{{ $user->created_at?->format('Y-m-d H:i:s') }}</p>
        </div>
    </section>
</div>
@endsection
