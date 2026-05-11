@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    @vite('resources/css/pages/dashboard.css')
@endpush

@section('content')
<div class="dashboardPage">
    <header class="dashboardHeader">
        <div>
            <p class="dashboardEyebrow">Panel administrativo</p>
            <h1 class="dashboardTitle">Dark Kitchens</h1>
        </div>

        <div class="dashboardUserInfo">
            <span class="dashboardUserName">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dashboardLogoutButton">Log out</button>
            </form>
        </div>
    </header>

    <main class="dashboardContent">
        <section class="dashboardHero">
            <h2 class="dashboardHeroTitle">Gestión central del negocio</h2>
            <p class="dashboardHeroText">
                Administra usuarios, roles y prepara la estructura para controlar menú, pedidos y operación de cocina.
            </p>
        </section>

        <section class="dashboardGrid">
            <a href="{{ route('admin.users.index') }}" class="dashboardActionCard">
                <span class="dashboardCardLabel">Activo</span>
                <h3>Usuarios</h3>
                <p>Gestiona cuentas de administradores, clientes y cocineros.</p>
            </a>

            <a href="{{ route('admin.roles.index') }}" class="dashboardActionCard">
                <span class="dashboardCardLabel">Activo</span>
                <h3>Roles</h3>
                <p>Consulta y administra roles sin romper los accesos base del sistema.</p>
            </a>

            <a href="{{ route('admin.products.index') }}" class="dashboardActionCard">
                <span class="dashboardCardLabel">Activo</span>
                <h3>Productos</h3>
                <p>Gestiona categorías, productos, disponibilidad y precios del menú.</p>
            </a>

            <div class="dashboardActionCard dashboardActionCardMuted">
                <span class="dashboardCardLabel">Próximamente</span>
                <h3>Pedidos</h3>
                <p>Visualiza pedidos, estados y actividad operativa del negocio.</p>
            </div>
        </section>
    </main>
</div>
@endsection
