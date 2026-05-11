@extends('layouts.app')

@section('title', 'Productos')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Productos</h1>
            <p class="adminUsersSubtitle">Gestiona el menú, precios y disponibilidad.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.dashboard') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
            <a href="{{ route('admin.categories.index') }}" class="adminUsersButton adminUsersButtonSecondary">Categorías</a>
            <a href="{{ route('admin.products.create') }}" class="adminUsersButton adminUsersButtonPrimary">Nuevo producto</a>
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

    <form method="GET" action="{{ route('admin.products.index') }}" class="adminUsersFilters">
        <div class="adminUsersFilterGroup">
            <label class="adminFormLabel" for="filter">Buscar</label>
            <input id="filter" class="adminFormControl" type="text" name="filter" value="{{ $filter }}" placeholder="Nombre, descripción o categoría">
        </div>

        <div class="adminUsersFilterGroup">
            <label class="adminFormLabel" for="category_id">Categoría</label>
            <select id="category_id" class="adminFormControl" name="category_id">
                <option value="">Todas</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="adminUsersFilterGroup">
            <label class="adminFormLabel" for="availability">Estado</label>
            <select id="availability" class="adminFormControl" name="availability">
                <option value="">Todos</option>
                <option value="available" @selected($availability === 'available')>Disponible</option>
                <option value="unavailable" @selected($availability === 'unavailable')>No disponible</option>
            </select>
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
            <a href="{{ route('admin.products.index') }}" class="adminUsersButton adminUsersButtonSecondary">Limpiar</a>
        </div>
    </form>

    <section class="adminUsersCard">
        <table class="adminUsersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Disponible</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name }}</td>
                        <td>${{ number_format((float) $product->price, 0, ',', '.') }}</td>
                        <td>{{ $product->is_available ? 'Sí' : 'No' }}</td>
                        <td>
                            <div class="adminUsersTableActions">
                                <a href="{{ route('admin.products.show', $product) }}" class="adminInlineAction">Ver</a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="adminInlineAction">Editar</a>

                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="adminInlineAction adminInlineActionDanger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="adminPagination">
            {{ $products->links() }}
        </div>
    </section>
</div>
@endsection
