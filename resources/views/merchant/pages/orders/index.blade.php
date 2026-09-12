@extends('layouts.main')
@section('breadcrumb')
    <a href="{{ route('merchant.orders.index') }}" class="flex items-center gap-2">
        Daftar Order
    </a>
@endsection
@php
    $indexRoute = fn() => route('merchant.orders.index');
    $statusBadge = [
        'pending' => 'badge-warning',
        'accepted' => 'badge-info',
        'rejected' => 'badge-error',
        'completed' => 'badge-success',
        'cancelled' => 'badge-ghost',
    ];
@endphp
@section('pages')
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h1 class="card-title text-lg mb-4"><b>Daftar Order Masuk</b></h1>

            <form method="GET" action="{{ $indexRoute() }}" class="mb-6">
                <div class="flex flex-col lg:flex-row gap-2">
                    <div class="form-control grow">
                        <input type="text" name="search" placeholder="Cari nomor invoice / nama kantor..."
                            value="{{ request('search') }}" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control w-full lg:w-52">
                        <select name="status" class="select select-bordered w-full" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            @foreach (\App\Models\Order::STATUS as $key => $label)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary w-full lg:w-auto"><i
                                class="ri-search-line"></i></button>
                        <a href="{{ $indexRoute() }}" class="btn btn-error w-full lg:w-auto"><i
                                class="ri-refresh-line"></i></a>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <x-sort-th column="invoice_number" label="No Invoice" />
                            <x-sort-th column="customer_id" label="Kantor" />
                            <x-sort-th column="delivery_date" label="Tanggal Kirim" />
                            <x-sort-th column="total_price" label="Total" />
                            <x-sort-th column="status" label="Status" />
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($models as $order)
                            <tr>
                                <td class="font-semibold">{{ $order->invoice_number }}</td>
                                <td>{{ $order->customer->name }}</td>
                                <td>{{ $order->delivery_date->translatedFormat('d M Y') }}</td>
                                <td>{{ $order->total_rp }}</td>
                                <td>
                                    <span class="badge {{ $statusBadge[$order->status] ?? 'badge-ghost' }}">
                                        {{ $order->status_val }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('merchant.orders.show', $order) }}" class="btn btn-primary btn-sm">
                                        Invoice
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-base-content/50">
                                    Belum ada pesanan masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $models->links('components/paginate') }}
            </div>
        </div>
    </div>
@endsection
