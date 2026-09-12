<?php

namespace App\Http\Controllers\Merchant;

use App\Helpers\QuerySearch;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $models = QuerySearch::apply(
            query: Order::with('customer')->where('merchant_id', $request->user()->id),
            request: $request,
            searchableColumns: ['invoice_number'],
            filterableColumns: ['status'],
            perPage: 10,
            defaultSort: ['created_at' => 'desc']
        );

        return view('merchant.pages.orders.index', get_defined_vars());
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->merchant_id === $request->user()->id, 404);

        $order->load(['items.menu', 'customer', 'merchant']);

        return view('merchant.pages.orders.show', get_defined_vars());
    }

    public function updateStatus(Request $request, Order $order)
    {
        abort_unless($order->merchant_id === $request->user()->id, 404);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['accepted', 'rejected', 'completed']),
            ],
        ]);

        $allowed = [
            'pending' => ['accepted', 'rejected'],
            'accepted' => ['completed'],
        ];

        if (! in_array($validated['status'], $allowed[$order->status] ?? [], true)) {
            return back()->with('error', 'Perubahan status tidak diizinkan.');
        }

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
