<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with(['status', 'orderDetails.product.category'])
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('client.orders.index', compact('orders'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $order = DB::transaction(function () use ($request, $validated): Order {
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($validated['product_id']);

            if (! $product->is_available) {
                abort(404);
            }

            $quantity = (int) $validated['quantity'];
            $unitPrice = (float) $product->price;
            $subtotal = $unitPrice * $quantity;

            $pendingStatus = Status::query()->firstOrCreate([
                'name' => 'pending',
            ]);

            $order = Order::query()->create([
                'user_id' => $request->user()->id,
                'order_date' => now(),
                'total_amount' => $subtotal,
                'status_id' => $pendingStatus->id,
            ]);

            $order->orderDetails()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);

            return $order;
        });

        return redirect()
            ->route('client.orders.show', $order)
            ->with('status', 'Pedido creado correctamente.');
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['status', 'orderDetails.product.category']);

        return view('client.orders.show', compact('order'));
    }
}
