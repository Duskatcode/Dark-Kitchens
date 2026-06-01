<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos | Admin</title>
    @vite('resources/css/pages/admin-users.css')
</head>
<body>
    <main class="admin-page">
        <section class="admin-card">
            <div class="admin-header">
                <div>
                    <h1>Pedidos</h1>
                    <p>Gestiona todos los pedidos registrados en el sistema.</p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Volver al dashboard</a>
            </div>

            @if(session('status'))
                <div class="alert success">{{ session('status') }}</div>
            @endif

            <form method="GET" action="{{ route('admin.orders.index') }}" class="admin-form" style="margin-bottom: 1rem;">
                <div class="form-row">
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

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Filtrar</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn-secondary">Limpiar</a>
                </div>
            </form>

            <div class="table-wrapper">
                <table>
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
                                    <span class="badge">{{ $order->status->name }}</span>
                                </td>
                                <td>${{ number_format((float) $order->total_amount, 0, ',', '.') }}</td>
                                <td>{{ $order->order_date }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn-secondary">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No hay pedidos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        </section>
    </main>
</body>
</html>
