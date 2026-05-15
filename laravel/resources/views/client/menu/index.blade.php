@extends('layouts.app')

@section('title', 'Menú')

@push('styles')
    @vite('resources/css/pages/client-menu.css')
@endpush

@section('content')
<div class="clientMenuPage">
    <header class="clientMenuHeader">
        <div>
            <p class="clientMenuEyebrow">Menú disponible</p>
            <h1 class="clientMenuTitle">Explora productos para tu pedido</h1>
            <p class="clientMenuSubtitle">Consulta productos activos, precios y categorías.</p>
        </div>

        <div class="clientMenuActions">
            <a href="{{ route('client.dashboard') }}" class="clientMenuButton clientMenuButtonSecondary">Volver</a>
        </div>
    </header>

    <form method="GET" action="{{ route('client.menu.index') }}" class="clientMenuFilters">
        <div class="clientMenuFilterGroup">
            <label for="filter">Buscar</label>
            <input id="filter" type="text" name="filter" value="{{ $filter }}" placeholder="Nombre, descripción o categoría">
        </div>

        <div class="clientMenuFilterGroup">
            <label for="category_id">Categoría</label>
            <select id="category_id" name="category_id">
                <option value="">Todas</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="clientMenuFilterActions">
            <button type="submit" class="clientMenuButton clientMenuButtonPrimary">Filtrar</button>
            <a href="{{ route('client.menu.index') }}" class="clientMenuButton clientMenuButtonSecondary">Limpiar</a>
        </div>
    </form>

    <section class="clientMenuGrid">
        @forelse ($products as $product)
            <article class="clientMenuCard">
                <div class="clientMenuCardHeader">
                    <span class="clientMenuCategory">{{ $product->category?->name }}</span>
                    <span class="clientMenuPrice">${{ number_format((float) $product->price, 0, ',', '.') }}</span>
                </div>

                <h2>{{ $product->name }}</h2>
                <p>{{ $product->description }}</p>

                <a href="{{ route('client.menu.show', $product) }}" class="clientMenuCardLink">Ver detalle</a>
            </article>
        @empty
            <div class="clientMenuEmpty">
                No hay productos disponibles con los filtros seleccionados.
            </div>
        @endforelse
    </section>

    <div class="clientMenuPagination">
        {{ $products->links() }}
    </div>
</div>
@endsection
