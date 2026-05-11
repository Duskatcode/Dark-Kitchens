@extends('layouts.app')

@section('title', 'Cook Dashboard')

@push('styles')
    @vite('resources/css/pages/dashboard.css')
@endpush

@section('content')
<div class="dashboardPage">
    <header class="dashboardHeader">
        <div>
            <p class="dashboardEyebrow">Panel de cocina</p>
            <h1 class="dashboardTitle">Operación de pedidos</h1>
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
            <h2 class="dashboardHeroTitle">Tareas de cocina</h2>
            <p class="dashboardHeroText">
                Próximamente este panel mostrará pedidos pendientes, preparación y actualización de estados.
            </p>
        </section>

        <section class="dashboardGrid">
            <div class="dashboardActionCard dashboardActionCardMuted">
                <span class="dashboardCardLabel">Próximamente</span>
                <h3>Pedidos pendientes</h3>
                <p>Lista de pedidos que aún deben aceptarse o prepararse.</p>
            </div>

            <div class="dashboardActionCard dashboardActionCardMuted">
                <span class="dashboardCardLabel">Próximamente</span>
                <h3>En preparación</h3>
                <p>Control de pedidos activos dentro de la cocina.</p>
            </div>
        </section>
    </main>
</div>
@endsection
