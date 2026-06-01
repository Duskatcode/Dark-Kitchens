<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos | Admin</title>
    @vite('resources/css/pages/admin-orders.css')
</head>
<body>
    <main class="adminOrdersPage">
        <div class="adminOrdersContainer">
            <section class="adminOrdersCard">
                <div class="adminOrdersHeader">
                    <div>
                        <h1>Pedidos</h1>
                        <p>Gestiona todos los pedidos registrados en el sistema.</p>
                    </div>

                    <a href="{{ route('admin.dashboard') }}" class="adminOrdersButtonSecondary">
                        Volver al dashboard
                    </a>
                </div>

                @if(session('status'))
                    <div class="adminOrdersAlert">{{ session('status') }}</div>
                @endif

                <form method="GET" action="{{ route('admin.orders.index') }}" class="adminOrdersToolbar">
                    <div class="adminOrdersField">
                        <label for="status">Filtrar por estado</label>
                        <select id="status" name="status">
                            <option value="">Todos</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->name }}" @selected($selectedStatus === $status->name)>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="adminOrdersActions">
                        <button type="submit" class="adminOrdersButton">Filtrar</button>
                        <a href="{{ route('admin.orders.index') }}" class="adminOrdersButtonSecondary">Limpiar</a>
                    </div>
                </form>

                <div class="adminOrdersTableWrapper">
                    <table class="adminOrdersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>Pedido #{{ $order->id }}</td>
                                    <td>{{ $order->user->name }} {{ $order->user->last_name }}</td>
                                    <td>
                                        <span class="adminOrdersBadge">{{ $order->status->name }}</span>
                                    </td>
                                    <td>${{ number_format((float) $order->total_amount, 0, ',', '.') }}</td>
                                    <td>{{ $order->order_date }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="adminOrdersButtonSecondary">
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="adminOrdersEmpty">No hay pedidos registrados.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="adminOrdersPagination">
                    {{ $orders->links() }}
                </div>
            </section>
        </div>
    </main>
</body>
</html>
