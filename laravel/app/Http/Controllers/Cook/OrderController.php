<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['user', 'status', 'orderDetails.product.category'])
            ->whereHas('status', function ($query): void {
                $query->whereIn('name', ['pending', 'in_progress']);
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('cook.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'status', 'orderDetails.product.category']);

        return view('cook.orders.show', compact('order'));
    }

    public function start(Order $order): RedirectResponse
    {
        $order->load('status');

        if ($order->status?->name !== 'pending') {
            return redirect()
                ->route('cook.orders.show', $order)
                ->withErrors([
                    'status' => 'Solo puedes iniciar pedidos en estado pending.',
                ]);
        }

        $order->update([
            'status_id' => $this->statusId('in_progress'),
        ]);

        return redirect()
            ->route('cook.orders.show', $order)
            ->with('status', 'Pedido marcado como en preparación.');
    }

    public function complete(Order $order): RedirectResponse
    {
        $order->load('status');

        if ($order->status?->name !== 'in_progress') {
            return redirect()
                ->route('cook.orders.show', $order)
                ->withErrors([
                    'status' => 'Solo puedes completar pedidos en estado in_progress.',
                ]);
        }

        $order->update([
            'status_id' => $this->statusId('completed'),
        ]);

        return redirect()
            ->route('cook.orders.index')
            ->with('status', 'Pedido completado correctamente.');
    }

    private function statusId(string $name): int
    {
        return Status::query()->firstOrCreate(['name' => $name])->id;
    }
}
