@extends('layouts.app')

@section('title', $product->name)

@push('styles')
    @vite('resources/css/pages/client-menu.css')
@endpush

@section('content')
<div class="clientMenuPage">
    <header class="clientMenuHeader">
        <div>
            <p class="clientMenuEyebrow">{{ $product->category?->name }}</p>
            <h1 class="clientMenuTitle">{{ $product->name }}</h1>
            <p class="clientMenuSubtitle">Detalle del producto seleccionado.</p>
        </div>

        <div class="clientMenuActions">
            <a href="{{ route('client.menu.index') }}" class="clientMenuButton clientMenuButtonSecondary">Volver al menú</a>
            <a href="{{ route('client.orders.index') }}" class="clientMenuButton clientMenuButtonSecondary">Mis pedidos</a>
        </div>
    </header>

    <section class="clientMenuDetail">
        <div class="clientMenuDetailCard">
            <span class="clientMenuCategory">{{ $product->category?->name }}</span>
            <h2>{{ $product->name }}</h2>
            <p>{{ $product->description }}</p>

            <div class="clientMenuDetailMeta">
                <span>Precio</span>
                <strong>${{ number_format((float) $product->price, 0, ',', '.') }}</strong>
            </div>

            <form method="POST" action="{{ route('client.orders.store') }}" class="clientOrderForm">
                @csrf

                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="clientMenuFilterGroup">
                    <label for="quantity">Cantidad</label>
                    <input
                        id="quantity"
                        type="number"
                        name="quantity"
                        value="{{ old('quantity', 1) }}"
                        min="1"
                        max="20"
                        required
                    >
                    @error('quantity')
                        <span class="clientMenuError">{{ $message }}</span>
                    @enderror
                    @error('product_id')
                        <span class="clientMenuError">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="clientMenuButton clientMenuButtonPrimary">
                    Crear pedido
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
