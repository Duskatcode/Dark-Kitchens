@extends('layouts.app')

@section('title', 'Dark Kitchens | Gestión para cocinas delivery')

@push('styles')
    @vite('resources/css/pages/home.css')
@endpush

@section('content')
<div class="homePage">
    <header class="homeNav">
        <a href="{{ route('home.index') }}" class="homeBrand">Dark Kitchens</a>

        <div class="homeNavActions">
            @auth
                <a href="{{ route('dashboard') }}" class="homeButton homeButtonPrimary">Ir al panel</a>
            @else
                <a href="{{ route('login') }}" class="homeButton homeButtonOutline">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="homeButton homeButtonPrimary">Crear cuenta</a>
            @endauth
        </div>
    </header>

    <main class="homeMain">
        <section class="homeHero">
            <div class="homeHeroContent">
                <span class="homeEyebrow">Gestión operativa para cocinas delivery</span>

                <h1 class="homeTitle">
                    Organiza pedidos, menú y tareas de cocina desde una sola plataforma.
                </h1>

                <p class="homeSubtitle">
                    Dark Kitchens ayuda a negocios de comida que trabajan por domicilios a reducir el desorden operativo,
                    centralizar pedidos y dar más claridad al equipo de cocina.
                </p>

                <div class="homeActions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="homeButton homeButtonPrimary">Entrar al dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="homeButton homeButtonPrimary">Comenzar ahora</a>
                        <a href="{{ route('login') }}" class="homeButton homeButtonOutline">Ya tengo cuenta</a>
                    @endauth
                </div>
            </div>

            <div class="homePanel" aria-label="Resumen de módulos de Dark Kitchens">
                <div class="homePanelHeader">
                    <span class="homePanelStatus"></span>
                    <span>Operación centralizada</span>
                </div>

                <div class="homePanelList">
                    <div class="homePanelItem">
                        <strong>Menú organizado</strong>
                        <span>Productos, categorías y disponibilidad.</span>
                    </div>

                    <div class="homePanelItem">
                        <strong>Pedidos centralizados</strong>
                        <span>Menos dependencia del chat y más trazabilidad.</span>
                    </div>

                    <div class="homePanelItem">
                        <strong>Cocina más clara</strong>
                        <span>Vista de tareas y estados para el equipo operativo.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="homeFeatures">
            <article class="homeFeatureCard">
                <span class="homeFeatureNumber">01</span>
                <h2>Administra el negocio</h2>
                <p>Gestiona usuarios, roles y la estructura base para controlar el menú.</p>
            </article>

            <article class="homeFeatureCard">
                <span class="homeFeatureNumber">02</span>
                <h2>Ordena la experiencia del cliente</h2>
                <p>Prepara un flujo donde el cliente pueda consultar menú y realizar pedidos.</p>
            </article>

            <article class="homeFeatureCard">
                <span class="homeFeatureNumber">03</span>
                <h2>Facilita el trabajo en cocina</h2>
                <p>Da al cocinero un panel enfocado en pedidos pendientes y preparación.</p>
            </article>
        </section>
    </main>
</div>
@endsection
