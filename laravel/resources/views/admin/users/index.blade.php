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

    <form method="GET" action="{{ route('admin.users.index') }}" class="adminUsersFilters">
        <div class="adminUsersFilterGroup">
            <label for="filter" class="adminFormLabel">Buscar</label>
            <input id="filter" type="search" name="filter" class="adminFormControl" value="{{ $filter }}" placeholder="Nombre, correo o rol">
        </div>

        <div class="adminUsersFilterGroup">
            <label for="records_per_page" class="adminFormLabel">Registros por página</label>
            <select id="records_per_page" name="records_per_page" class="adminFormControl">
                @foreach ($recordsPerPageOptions as $option)
                    <option value="{{ $option }}" @selected($recordsPerPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>

        <div class="adminUsersFilterActions">
            <a href="{{ route('admin.users.index') }}" class="adminUsersButton adminUsersButtonSecondary">Limpiar</a>
            <button type="submit" class="adminUsersButton adminUsersButtonPrimary">Filtrar</button>
        </div>
    </form>

    @include('admin.users.partials.user-table', ['users' => $users])
</div>
@endsection
