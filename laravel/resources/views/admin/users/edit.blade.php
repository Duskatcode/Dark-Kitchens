@extends('layouts.app')

@section('title', 'Editar usuario')

@push('styles')
    @vite('resources/css/pages/admin-users.css')
@endpush

@section('content')
<div class="adminUsersPage">
    <header class="adminUsersHeader">
        <div>
            <h1 class="adminUsersTitle">Editar usuario #{{ $user->id }}</h1>
            <p class="adminUsersSubtitle">Actualiza los datos y rol del usuario.</p>
        </div>
    </header>

    <section class="adminUsersCard">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="adminUserForm">
            @csrf
            @method('PUT')
            @php($submitLabel = 'Actualizar usuario')
            @include('admin.users._form')
        </form>
    </section>
</div>
@endsection
