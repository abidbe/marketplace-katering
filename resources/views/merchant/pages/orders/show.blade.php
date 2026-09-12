@extends('layouts.main')
@section('breadcrumb')
    <a href="{{ route('merchant.orders.index') }}" class="flex items-center gap-2">
        <i class="ri-arrow-left-line"></i> Daftar Order
    </a>
@endsection
@php
    $statusBadge = [
        'pending' => 'badge-warning',
        'accepted' => 'badge-info',
        'rejected' => 'badge-error',
        'completed' => 'badge-success',
        'cancelled' => 'badge-ghost',
    ];
    $transitions = [
        'pending' => ['accepted' => 'Terima Order', 'rejected' => 'Tolak Order'],
        'accepted' => ['completed' => 'Tandai Selesai'],
    ];
@endphp
@section('pages')
    <div class="card bg-base-100 shadow-xl max-w-3xl mx-auto">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row justify-between gap-4 border-b border-base-300 pb-4">
                <div>
                    <h1 class="text-2xl font-bold">INVOICE</h1>
                    <p class="text-base-content/70">{{ $order->invoice_number }}</p>
                </div>
                <div class="sm:text-right">
                    <span class="badge {{ $statusBadge[$order->status] ?? 'badge-ghost' }} badge-lg font-semibold">
                        {{ $order->status_val }}
                    </span>
                    <p class="text-sm mt-1 text-base-content/60">
                        Masuk {{ $order->created_at->translatedFormat('d M Y H:i') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-4 border-b border-base-300">
                <div>
                    <h3 class="font-semibold text-sm uppercase text-base-content/50 mb-1">Kantor Pemesan</h3>
                    <p class="font-bold">{{ $order->customer->name }}</p>
                    <p class="text-sm text-base-content/70">{{ $order->customer->email }}</p>
                    @if ($order->customer->phone)
                        <p class="text-sm text-base-content/70">{{ $order->customer->phone }}</p>
                    @endif
                </div>
                <div class="sm:text-right">
                    <h3 class="font-semibold text-sm uppercase text-base-content/50 mb-1">Pengiriman</h3>
                    <p class="text-sm mt-1">Tanggal:
                        <b>{{ $order->delivery_date->translatedFormat('d M Y') }}</b>
                    </p>
                    @if ($order->customer->address)
                        <p class="text-sm text-base-content/70">{{ $order->customer->address }}</p>
                    @endif
                </div>
            </div>

            @if ($order->note)
                <p class="text-sm py-2 border-b border-base-300">
                    <span class="font-semibold">Catatan:</span> {{ $order->note }}
                </p>
            @endif

            <div class="overflow-x-auto py-2">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th class="text-right">Harga</th>
                            <th class="text-center">Porsi</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->menu_name }}</td>
                                <td class="text-right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->portions }}</td>
                                <td class="text-right">{{ $item->subtotal_rp }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">TOTAL</th>
                            <th class="text-right text-lg text-primary">{{ $order->total_rp }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="card-actions justify-between mt-4">
                <a href="{{ route('merchant.orders.index') }}" class="btn btn-ghost">
                    <i class="ri-arrow-left-line mr-1"></i> Kembali
                </a>
                <div class="flex gap-2 flex-wrap justify-end">
                    <a href="{{ route('invoice.print', $order) }}" target="_blank" class="btn btn-outline">
                        <i class="ri-printer-line mr-1"></i> Cetak
                    </a>
                    @foreach ($transitions[$order->status] ?? [] as $next => $label)
                        <form method="POST" action="{{ route('merchant.orders.status', $order) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $next }}">
                            <button type="submit"
                                class="btn {{ $next === 'rejected' ? 'btn-error' : ($next === 'completed' ? 'btn-success' : 'btn-primary') }}">
                                {{ $label }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
