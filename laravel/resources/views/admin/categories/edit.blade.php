@extends('layouts.app')

@section('title', 'Editar categoría')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Editar categoría</h1>
            <p class="adminUsersSubtitle">Actualiza la información de la categoría.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.categories.index') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
        </div>
    </header>

    <section class="adminUsersCard">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @method('PUT')
            @include('admin.categories._form', [
                'category' => $category,
                'buttonText' => 'Guardar cambios',
            ])
        </form>
    </section>
</div>
@endsection
