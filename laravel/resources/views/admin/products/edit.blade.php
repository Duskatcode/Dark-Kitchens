@extends('layouts.app')

@section('title', 'Editar producto')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Editar producto</h1>
            <p class="adminUsersSubtitle">Actualiza precio, categoría y disponibilidad.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.products.index') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
        </div>
    </header>

    <section class="adminUsersCard">
        <form method="POST" action="{{ route('admin.products.update', $product) }}">
            @method('PUT')
            @include('admin.products._form', [
                'product' => $product,
                'categories' => $categories,
                'buttonText' => 'Guardar cambios',
            ])
        </form>
    </section>
</div>
@endsection
