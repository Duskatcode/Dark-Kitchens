@extends('layouts.app')

@section('title', 'Client Dashboard')

@push('styles')
    @vite('resources/css/pages/dashboard.css')
@endpush

@section('content')
<div class="dashboardPage">
    <header class="dashboardHeader">
        <div>
            <p class="dashboardEyebrow">Área cliente</p>
            <h1 class="dashboardTitle">Bienvenido, {{ Auth::user()->name }}</h1>
        </div>

        <div class="dashboardUserInfo">
            <span class="dashboardUserName">{{ Auth::user()->email }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dashboardLogoutButton">Log out</button>
            </form>
        </div>
    </header>

    <main class="dashboardContent">
        <section class="dashboardHero">
            <h2 class="dashboardHeroTitle">Tu experiencia de pedidos</h2>
            <p class="dashboardHeroText">
                Desde aquí podrás consultar el menú y revisar tus pedidos cuando el flujo de compra esté habilitado.
            </p>
        </section>

        <section class="dashboardGrid">
            <a href="{{ route('client.menu.index') }}" class="dashboardActionCard">
                <span class="dashboardCardLabel">Activo</span>
                <h3>Ver menú</h3>
                <p>Explora productos disponibles, precios y categorías.</p>
            </a>

            <a href="{{ route('client.orders.index') }}" class="dashboardActionCard">
                <span class="dashboardCardLabel">Activo</span>
                <h3>Mis pedidos</h3>
                <p>Consulta el estado de tus pedidos y su historial.</p>
            </a>
        </section>
    </main>
</div>
@endsection
