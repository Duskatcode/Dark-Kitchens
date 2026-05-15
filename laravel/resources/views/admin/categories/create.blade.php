@extends('layouts.app')

@section('title', 'Nueva categoría')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Nueva categoría</h1>
            <p class="adminUsersSubtitle">Crea una categoría para organizar productos del menú.</p>
        </div>

        <div class="adminUsersActions">
            <a href="{{ route('admin.categories.index') }}" class="adminUsersButton adminUsersButtonSecondary">Volver</a>
        </div>
    </header>

    <section class="adminUsersCard">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @include('admin.categories._form', [
                'category' => null,
                'buttonText' => 'Crear categoría',
            ])
        </form>
    </section>
</div>
@endsection
