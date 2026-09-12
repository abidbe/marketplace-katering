<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\QuerySearch;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $models = QuerySearch::apply(
            query: Order::with('merchant')->where('customer_id', $request->user()->id),
            request: $request,
            searchableColumns: ['invoice_number'],
            filterableColumns: ['status'],
            perPage: 10,
            defaultSort: ['created_at' => 'desc']
        );

        return view('customer.pages.orders.index', get_defined_vars());
    }

    public function form(Request $request)
    {
        $merchant = User::findOrFail($request->integer('merchant'));
        abort_unless($merchant->role === 'merchant' && $merchant->is_active, 404);

        $menus = $merchant->menus()
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('customer.pages.orders.form', get_defined_vars());
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'merchant_id' => ['required', 'exists:users,id'],
            'delivery_date' => ['required', 'date', 'after_or_equal:today'],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer'],
            'items.*.portions' => ['required', 'integer', 'min:1', 'max:10000'],
        ], [], [
            'merchant_id' => 'Katering',
            'delivery_date' => 'Tanggal Kirim',
            'items' => 'Menu Pesanan',
            'items.*.menu_id' => 'Menu',
            'items.*.portions' => 'Jumlah Porsi',
        ]);

        abort_unless(
            User::whereKey($validated['merchant_id'])->where('role', 'merchant')->where('is_active', true)->exists(),
            404
        );

        $ids = collect($validated['items'])->pluck('menu_id');

        $menus = Menu::where('user_id', $validated['merchant_id'])
            ->where('is_active', true)
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'price'])
            ->keyBy('id');

        if ($menus->count() !== $ids->count() || $ids->unique()->count() !== $ids->count()) {
            throw ValidationException::withMessages([
                'items' => 'Ada menu yang sudah tidak tersedia atau dipilih lebih dari sekali.',
            ]);
        }

        if ($request->ajax()) {
            return;
        }

        $order = DB::transaction(function () use ($validated, $menus, $request) {
            $order = Order::create([
                'invoice_number' => Order::nextInvoiceNumber($validated['delivery_date']),
                'customer_id' => $request->user()->id,
                'merchant_id' => $validated['merchant_id'],
                'delivery_date' => $validated['delivery_date'],
                'note' => $validated['note'] ?? null,
                'total_price' => collect($validated['items'])->sum(
                    fn ($row) => (int) $menus[$row['menu_id']]->price * (int) $row['portions']
                ),
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $row) {
                $menu = $menus[$row['menu_id']];
                $order->items()->create([
                    'menu_id' => (int) $row['menu_id'],
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'portions' => (int) $row['portions'],
                    'subtotal' => (int) $menu->price * (int) $row['portions'],
                ]);
            }

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibuat.');
    }


    public function show(Request $request, Order $order)
    {
        abort_unless($order->customer_id === $request->user()->id, 404);

        $order->load(['items.menu', 'merchant', 'customer']);

        return view('customer.pages.orders.show', get_defined_vars());
    }

    /**
     * Halaman cetak invoice: pembeli, penjual, atau admin.
     */
    public function cetak(Request $request, Order $order)
    {
        $user = $request->user();

        abort_unless(
            $user->role === 'admin' || $order->customer_id === $user->id || $order->merchant_id === $user->id,
            404
        );

        $order->load(['items', 'customer', 'merchant']);

        return view('invoice.print', get_defined_vars());
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->customer_id === $request->user()->id, 404);

        if (! $order->isEditableByCustomer()) {
            return back()->with('error', 'Pesanan yang sudah diproses tidak bisa dibatalkan.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Pesanan dibatalkan.');
    }
}
