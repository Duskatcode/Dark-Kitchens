@extends('layouts.app')

@section('title', 'Administración de usuarios')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Usuarios</h1>
            <p class="adminUsersSubtitle">Módulo administrativo de gestión de usuarios.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('dashboard') }}" class="adminUsersButton adminUsersButtonSecondary">Dashboard</a>
            <a href="{{ route('admin.users.create') }}" class="adminUsersButton adminUsersButtonPrimary">Crear usuario</a>
        </div>
    </header>

    @if (session('status'))
        <div class="adminAlert adminAlertSuccess">{{ session('status') }}</div>
    @endif

    @if ($errors->has('delete'))
        <div class="adminAlert adminAlertError">{{ $errors->first('delete') }}</div>
    @endif

    <section class="adminUsersCard">
        <table class="adminUsersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role?->name ?? 'Sin rol' }}</td>
                        <td class="adminUsersTableActions">
                            <a href="{{ route('admin.users.show', $user) }}" class="adminInlineAction">Ver</a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="adminInlineAction">Editar</a>

                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="adminInlineAction adminInlineActionDanger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No hay usuarios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="adminPagination">
            {{ $users->links() }}
        </div>
    </section>
</div>
@endsection
