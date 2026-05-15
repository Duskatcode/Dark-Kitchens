@extends('layouts.app')

@section('title', 'Pedidos de cocina')

@push('styles')
    @vite('resources/css/pages/cook-orders.css')
@endpush

@section('content')
<div class="cookOrdersPage">
    <header class="cookOrdersHeader">
        <div>
            <p class="cookOrdersEyebrow">Cola de cocina</p>
            <h1 class="cookOrdersTitle">Pedidos pendientes y en preparación</h1>
            <p class="cookOrdersSubtitle">Gestiona el avance operativo de los pedidos activos.</p>
        </div>

        <div class="cookOrdersActions">
            <a href="{{ route('cook.dashboard') }}" class="cookOrdersButton cookOrdersButtonSecondary">Volver</a>
        </div>
    </header>

    @if (session('status'))
        <div class="cookOrdersAlert cookOrdersAlertSuccess">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="cookOrdersAlert cookOrdersAlertError">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="cookOrdersGrid">
        @forelse ($orders as $order)
            <article class="cookOrderCard">
                <div class="cookOrderCardHeader">
                    <span class="cookOrderStatus cookOrderStatus-{{ $order->status?->name }}">{{ $order->status?->name }}</span>
                    <span class="cookOrderTotal">${{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                </div>

                <h2>Pedido #{{ $order->id }}</h2>
                <p>Cliente: {{ $order->user?->name }} {{ $order->user?->last_name }}</p>
                <p>Fecha: {{ $order->order_date?->format('Y-m-d H:i') }}</p>
                <p>{{ $order->orderDetails->count() }} producto(s)</p>

                <a href="{{ route('cook.orders.show', $order) }}" class="cookOrderLink">Ver detalle</a>
            </article>
        @empty
            <div class="cookOrdersEmpty">
                No hay pedidos pendientes ni en preparación.
            </div>
        @endforelse
    </section>

    <div class="cookOrdersPagination">
        {{ $orders->links() }}
    </div>
</div>
@endsection
