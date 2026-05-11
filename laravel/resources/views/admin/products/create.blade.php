@extends('layouts.app')

@section('title', 'Nuevo producto')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Nuevo producto</h1>
            <p class="adminUsersSubtitle">Crea un producto para el menú.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.products.index') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
        </div>
    </header>

    <section class="adminUsersCard">
        <form method="POST" action="{{ route('admin.products.store') }}">
            @include('admin.products._form', [
                'product' => null,
                'categories' => $categories,
                'buttonText' => 'Crear producto',
            ])
        </form>
    </section>
</div>
@endsection
