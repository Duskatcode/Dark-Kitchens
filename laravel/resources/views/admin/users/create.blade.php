@extends('layouts.app')

@section('title', 'Crear usuario')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Crear usuario</h1>
            <p class="adminUsersSubtitle">Nuevo usuario para el panel administrativo.</p>
        </div>
    </header>

    <section class="adminUsersCard">
        <form method="POST" action="{{ route('admin.users.store') }}" class="adminUserForm">
            @csrf
            @php($submitLabel = 'Guardar usuario')
            @include('admin.users._form')
        </form>
    </section>
</div>
@endsection
