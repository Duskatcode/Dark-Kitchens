@extends('layouts.app')

@section('title', 'Detalle de producto')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">{{ $product->name }}</h1>
            <p class="adminUsersSubtitle">Detalle del producto.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.products.index') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
            <a href="{{ route('admin.products.edit', $product) }}" class="adminUsersButton adminUsersButtonPrimary">Editar</a>
        </div>
    </header>

    <section class="adminUsersCard">
        <div class="adminUserDetailGrid">
            <div>
                <p class="adminDetailLabel">Categoría</p>
                <p class="adminDetailValue">{{ $product->category?->name }}</p>
            </div>

            <div>
                <p class="adminDetailLabel">Precio</p>
                <p class="adminDetailValue">${{ number_format((float) $product->price, 0, ',', '.') }}</p>
            </div>

            <div>
                <p class="adminDetailLabel">Disponibilidad</p>
                <p class="adminDetailValue">{{ $product->is_available ? 'Disponible' : 'No disponible' }}</p>
            </div>

            <div>
                <p class="adminDetailLabel">Creado</p>
                <p class="adminDetailValue">{{ $product->created_at?->format('Y-m-d H:i') }}</p>
            </div>

            <div class="adminFormGroupWide">
                <p class="adminDetailLabel">Descripción</p>
                <p class="adminDetailValue">{{ $product->description }}</p>
            </div>
        </div>
    </section>
</div>
@endsection
