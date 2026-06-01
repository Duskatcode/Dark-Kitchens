<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido #{{ $order->id }} | Admin</title>
    @vite('resources/css/pages/admin-users.css')
</head>
<body>
    <main class="admin-page">
        <section class="admin-card">
            <div class="admin-header">
                <div>
                    <h1>Pedido #{{ $order->id }}</h1>
                    <p>Detalle completo del pedido.</p>
                </div>

                <a href="{{ route('admin.orders.index') }}" class="btn-secondary">Volver a pedidos</a>
            </div>

            @if(session('status'))
                <div class="alert success">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="detail-grid">
                <div>
                    <strong>Cliente</strong>
                    <p>{{ $order->user->name }} {{ $order->user->last_name }}</p>
                    <p>{{ $order->user->email }}</p>
                </div>

                <div>
                    <strong>Estado actual</strong>
                    <p><span class="badge">{{ $order->status->name }}</span></p>
                </div>

                <div>
                    <strong>Total</strong>
                    <p>${{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                </div>

                <div>
                    <strong>Fecha</strong>
                    <p>{{ $order->order_date }}</p>
                </div>
            </div>

            <h2>Productos</h2>

            <div class="table-wrapper">
                <table>
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

            <h2>Gestión</h2>

            <div class="admin-actions">
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="admin-form">
                    @csrf
                    @method('PATCH')

                    <div class="form-row">
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
                    </div>

                    <button type="submit" class="btn-primary">Actualizar estado</button>
                </form>

                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn-danger">
                        Eliminar pedido
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
