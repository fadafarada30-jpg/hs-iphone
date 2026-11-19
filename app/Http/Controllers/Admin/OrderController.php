<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['user', 'items'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latestFirst()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
            'counts' => [
                'total' => Order::count(),
                'processing' => Order::where('status', OrderStatus::Processing)->count(),
                'shipped' => Order::where('status', OrderStatus::Shipped)->count(),
                'completed' => Order::where('status', OrderStatus::Completed)->count(),
            ],
        ]);
    }

    public function advance(Order $order): RedirectResponse
    {
        $this->authorize('advance', $order);

        $next = match ($order->status) {
            OrderStatus::AwaitingPayment => OrderStatus::Processing,
            OrderStatus::Processing => OrderStatus::Shipped,
            OrderStatus::Shipped => OrderStatus::Completed,
            default => null,
        };

        if ($next === null) {
            return back()->with('error', 'Status tidak dapat dimajukan.');
        }

        $order->update(['status' => $next]);

        if ($next === OrderStatus::Completed) {
            $order->items->each(fn ($item) => $item->listing?->update(['status' => ListingStatus::Sold]));
        }

        return back()->with('success', "Status pesanan diubah ke: {$next->label()}");
    }
}
