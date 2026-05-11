@extends('layouts.app')

@section('title', 'Mis pedidos')

@push('styles')
    @vite('resources/css/pages/client-menu.css')
@endpush

@section('content')
<div class="clientMenuPage">
    <header class="clientMenuHeader">
        <div>
            <p class="clientMenuEyebrow">Historial</p>
            <h1 class="clientMenuTitle">Mis pedidos</h1>
            <p class="clientMenuSubtitle">Consulta tus pedidos y el estado actual de cada uno.</p>
        </div>

        <div class="clientMenuActions">
            <a href="{{ route('client.dashboard') }}" class="clientMenuButton clientMenuButtonSecondary">Volver</a>
            <a href="{{ route('client.menu.index') }}" class="clientMenuButton clientMenuButtonPrimary">Ver menú</a>
        </div>
    </header>

    @if (session('status'))
        <div class="clientMenuAlert">{{ session('status') }}</div>
    @endif

    <section class="clientMenuGrid">
        @forelse ($orders as $order)
            <article class="clientMenuCard">
                <div class="clientMenuCardHeader">
                    <span class="clientMenuCategory">{{ $order->status?->name }}</span>
                    <span class="clientMenuPrice">${{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                </div>

                <h2>Pedido #{{ $order->id }}</h2>
                <p>{{ $order->order_date?->format('Y-m-d H:i') }}</p>
                <p>{{ $order->orderDetails->count() }} producto(s)</p>

                <a href="{{ route('client.orders.show', $order) }}" class="clientMenuCardLink">Ver detalle</a>
            </article>
        @empty
            <div class="clientMenuEmpty">
                Todavía no tienes pedidos registrados.
            </div>
        @endforelse
    </section>

    <div class="clientMenuPagination">
        {{ $orders->links() }}
    </div>
</div>
@endsection
