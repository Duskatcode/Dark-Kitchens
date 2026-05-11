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

            <div class="clientMenuNotice">
                La creación de pedidos estará disponible en la siguiente fase del MVP.
            </div>
        </div>
    </section>
</div>
@endsection
