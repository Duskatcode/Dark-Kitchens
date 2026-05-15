@extends('layouts.app')

@section('title', 'Pedido #' . $order->id)

@push('styles')
    @vite('resources/css/pages/cook-orders.css')
@endpush

@section('content')
<div class="cookOrdersPage">
    <header class="cookOrdersHeader">
        <div>
            <p class="cookOrdersEyebrow">Pedido {{ $order->status?->name }}</p>
            <h1 class="cookOrdersTitle">Pedido #{{ $order->id }}</h1>
            <p class="cookOrdersSubtitle">
                Cliente: {{ $order->user?->name }} {{ $order->user?->last_name }} · {{ $order->order_date?->format('Y-m-d H:i') }}
            </p>
        </div>

        <div class="cookOrdersActions">
            <a href="{{ route('cook.orders.index') }}" class="cookOrdersButton cookOrdersButtonSecondary">Volver a cola</a>
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

    <section class="cookOrderDetail">
        <div class="cookOrderDetailCard">
            <span class="cookOrderStatus cookOrderStatus-{{ $order->status?->name }}">{{ $order->status?->name }}</span>

            <h2>Productos del pedido</h2>

            <div class="cookOrderItems">
                @foreach ($order->orderDetails as $detail)
                    <div class="cookOrderItem">
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

            <div class="cookOrderTotalRow">
                <span>Total</span>
                <strong>${{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong>
            </div>

            <div class="cookOrderStateActions">
                @if ($order->status?->name === 'pending')
                    <form method="POST" action="{{ route('cook.orders.start', $order) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="cookOrdersButton cookOrdersButtonPrimary">Iniciar preparación</button>
                    </form>
                @endif

                @if ($order->status?->name === 'in_progress')
                    <form method="POST" action="{{ route('cook.orders.complete', $order) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="cookOrdersButton cookOrdersButtonPrimary">Completar pedido</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
