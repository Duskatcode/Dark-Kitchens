@extends('layouts.app')

@section('title', 'Pedido #' . $order->id)

@push('styles')
    @vite('resources/css/pages/client-menu.css')
@endpush

@section('content')
<div class="clientMenuPage">
    <header class="clientMenuHeader">
        <div>
            <p class="clientMenuEyebrow">Pedido {{ $order->status?->name }}</p>
            <h1 class="clientMenuTitle">Pedido #{{ $order->id }}</h1>
            <p class="clientMenuSubtitle">{{ $order->order_date?->format('Y-m-d H:i') }}</p>
        </div>

        <div class="clientMenuActions">
            <a href="{{ route('client.orders.index') }}" class="clientMenuButton clientMenuButtonSecondary">Mis pedidos</a>
            <a href="{{ route('client.menu.index') }}" class="clientMenuButton clientMenuButtonPrimary">Ver menú</a>
        </div>
    </header>

    @if (session('status'))
        <div class="clientMenuAlert">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="clientMenuAlert clientMenuAlertError">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="clientMenuDetail">
        <div class="clientMenuDetailCard">
            <span class="clientMenuCategory clientMenuStatus-{{ $order->status?->name }}">{{ $order->status?->name }}</span>

            <h2>Resumen del pedido</h2>

            <div class="clientOrderItems">
                @foreach ($order->orderDetails as $detail)
                    <div class="clientOrderItem">
                        <div>
                            <strong>{{ $detail->product?->name }}</strong>
                            <span>{{ $detail->product?->category?->name }}</span>
                            <span>Cantidad: {{ $detail->quantity }}</span>
                        </div>

                        <div>
                            <span>${{ number_format((float) $detail->unit_price, 0, ',', '.') }} c/u</span>
                            <strong>${{ number_format((float) $detail->subtotal, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="clientMenuDetailMeta">
                <span>Total</span>
                <strong>${{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong>
            </div>

            @if ($order->status?->name === 'pending')
                <form method="POST" action="{{ route('client.orders.cancel', $order) }}" class="clientOrderCancelForm">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn-danger">
                        Cancelar pedido
                    </button>
                </form>
            @endif
        </div>
    </section>
</div>
@endsection
