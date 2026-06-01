<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const ALLOWED_TRANSITIONS = [
        'pending' => ['in_progress', 'cancelled'],
        'in_progress' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function index(Request $request): View
    {
        $statuses = Status::query()
            ->orderBy('name')
            ->get();

        $selectedStatus = $request->query('status');

        $orders = Order::query()
            ->with(['user', 'status', 'orderDetails.product.category'])
            ->when($selectedStatus, function ($query, string $selectedStatus): void {
                $query->whereHas('status', function ($statusQuery) use ($selectedStatus): void {
                    $statusQuery->where('name', $selectedStatus);
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'statuses', 'selectedStatus'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'status', 'orderDetails.product.category']);

        $statuses = Status::query()
            ->orderBy('name')
            ->get();

        $allowedNextStatuses = $this->allowedNextStatuses($order);

        return view('admin.orders.show', compact('order', 'statuses', 'allowedNextStatuses'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status_id' => ['required', 'integer', Rule::exists('statuses', 'id')],
        ]);

        $order->load('status');

        $currentStatus = $order->status?->name;
        $nextStatus = Status::query()->findOrFail($validated['status_id']);

        if (! $this->canTransition($currentStatus, $nextStatus->name)) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->withErrors([
                    'status_id' => "No se puede cambiar el pedido de {$currentStatus} a {$nextStatus->name}.",
                ]);
        }

        $order->update([
            'status_id' => $nextStatus->id,
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('status', 'Estado del pedido actualizado correctamente.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->load('status');

        if ($order->status?->name !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->withErrors([
                    'delete' => 'Solo se pueden eliminar pedidos en estado pending.',
                ]);
        }

        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'Pedido eliminado correctamente.');
    }

    private function allowedNextStatuses(Order $order): array
    {
        $currentStatus = $order->status?->name;

        return self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
    }

    private function canTransition(?string $currentStatus, string $nextStatus): bool
    {
        if (! $currentStatus) {
            return false;
        }

        return in_array($nextStatus, self::ALLOWED_TRANSITIONS[$currentStatus] ?? [], true);
    }
}
