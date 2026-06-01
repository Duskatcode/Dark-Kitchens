<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido #{{ $order->id }} | Admin</title>
    @vite('resources/css/pages/admin-orders.css')
</head>
<body>
    <main class="adminOrdersPage">
        <div class="adminOrdersContainer">
            <section class="adminOrdersCard">
                <div class="adminOrdersHeader">
                    <div>
                        <h1>Pedido #{{ $order->id }}</h1>
                        <p>Detalle completo del pedido.</p>
                    </div>

                    <a href="{{ route('admin.orders.index') }}" class="adminOrdersButtonSecondary">
                        Volver a pedidos
                    </a>
                </div>

                @if(session('status'))
                    <div class="adminOrdersAlert">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div class="adminOrdersAlert adminOrdersAlertError">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="adminOrdersDetailGrid">
                    <div class="adminOrdersDetailItem">
                        <strong>Cliente</strong>
                        <p>{{ $order->user->name }} {{ $order->user->last_name }}</p>
                        <p>{{ $order->user->email }}</p>
                    </div>

                    <div class="adminOrdersDetailItem">
                        <strong>Estado actual</strong>
                        <p><span class="adminOrdersBadge">{{ $order->status->name }}</span></p>
                    </div>

                    <div class="adminOrdersDetailItem">
                        <strong>Total</strong>
                        <p>${{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                    </div>

                    <div class="adminOrdersDetailItem">
                        <strong>Fecha</strong>
                        <p>{{ $order->order_date }}</p>
                    </div>
                </div>

                <h2 class="adminOrdersSectionTitle">Productos</h2>

                <div class="adminOrdersTableWrapper">
                    <table class="adminOrdersTable">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderDetails as $detail)
                                <tr>
                                    <td>{{ $detail->product->name }}</td>
                                    <td>{{ $detail->product->category->name }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>${{ number_format((float) $detail->unit_price, 0, ',', '.') }}</td>
                                    <td>${{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h2 class="adminOrdersSectionTitle">Gestión</h2>

                <div class="adminOrdersToolbar">
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="adminOrdersField">
                        @csrf
                        @method('PATCH')

                        <label for="status_id">Cambiar estado</label>
                        <select id="status_id" name="status_id">
                            <option value="">Selecciona un estado</option>
                            @foreach($statuses as $status)
                                <option
                                    value="{{ $status->id }}"
                                    @disabled(! in_array($status->name, $allowedNextStatuses, true))
                                >
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="adminOrdersButton">
                            Actualizar estado
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="adminOrdersButtonDanger">
                            Eliminar pedido
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
