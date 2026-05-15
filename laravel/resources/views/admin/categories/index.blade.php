@extends('layouts.app')

@section('title', 'Categorías')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Categorías</h1>
            <p class="adminUsersSubtitle">Gestiona la estructura principal del menú.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.dashboard') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
            <a href="{{ route('admin.categories.create') }}" class="adminUsersButton adminUsersButtonPrimary">Nueva categoría</a>
        </div>
    </header>

    @if (session('status'))
        <div class="adminAlert adminAlertSuccess">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="adminAlert adminAlertError">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="GET" action="{{ route('admin.categories.index') }}" class="adminUsersFilters">
        <div class="adminUsersFilterGroup">
            <label class="adminFormLabel" for="filter">Buscar</label>
            <input id="filter" class="adminFormControl" type="text" name="filter" value="{{ $filter }}" placeholder="Nombre de categoría">
        </div>

        <div class="adminUsersFilterGroup">
            <label class="adminFormLabel" for="records_per_page">Registros</label>
            <select id="records_per_page" class="adminFormControl" name="records_per_page">
                @foreach ($recordsPerPageOptions as $option)
                    <option value="{{ $option }}" @selected($recordsPerPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>

        <div class="adminUsersFilterActions">
            <button type="submit" class="adminUsersButton adminUsersButtonPrimary">Filtrar</button>
            <a href="{{ route('admin.categories.index') }}" class="adminUsersButton adminUsersButtonSecondary">Limpiar</a>
        </div>
    </form>

    <section class="adminUsersCard">
        <table class="adminUsersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Productos</th>
                    <th>Creada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>{{ $category->created_at?->format('Y-m-d') }}</td>
                        <td>
                            <div class="adminUsersTableActions">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="adminInlineAction">Editar</a>

                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta categoría?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="adminInlineAction adminInlineActionDanger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No hay categorías registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="adminPagination">
            {{ $categories->links() }}
        </div>
    </section>
</div>
@endsection
