@extends('layouts.main')
@section('breadcrumb')
    <a href="{{ route('caterings.index') }}" class="flex items-center gap-2">
        Cari Katering
    </a>
@endsection
@php
    $indexRoute = fn() => route('caterings.index');
@endphp
@section('pages')
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h1 class="card-title text-lg mb-4"><b>Cari Katering</b></h1>

            <form method="GET" action="{{ $indexRoute() }}" class="mb-6">
                <div class="flex flex-col lg:flex-row gap-2">
                    <div class="form-control grow">
                        <input type="text" name="search" placeholder="Cari nama katering / deskripsi..."
                            value="{{ request('search') }}" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control w-full lg:w-48">
                        <select name="city" class="select select-bordered w-full" onchange="this.form.submit()">
                            <option value="">Semua Kota</option>
                            @foreach ($cities as $c)
                                <option value="{{ $c }}" {{ request('city') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control w-full lg:w-48">
                        <select name="category" class="select select-bordered w-full" onchange="this.form.submit()">
                            <option value="">Semua Jenis Makanan</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary w-full lg:w-auto">
                            <i class="ri-search-line"></i>
                        </button>
                        <a href="{{ $indexRoute() }}" class="btn btn-error w-full lg:w-auto">
                            <i class="ri-refresh-line"></i>
                        </a>
                    </div>
                </div>
            </form>

            @if ($models->count() === 0)
                <div class="text-center py-12 text-base-content/50">
                    <i class="ri-store-off-line text-5xl mb-3 block"></i>
                    <p>Belum ada katering yang cocok.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach ($models as $merchant)
                        <div class="card bg-base-200 shadow-sm hover:shadow-lg transition-shadow">
                            <div class="card-body">
                                <h2 class="card-title text-base">{{ $merchant->company_name }}</h2>
                                <p class="text-sm flex items-center gap-1 text-base-content/70">
                                    <i class="ri-map-pin-line"></i> {{ $merchant->city }}
                                </p>
                                <p class="text-sm line-clamp-2 min-h-[2.5rem]">{{ $merchant->description }}</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach ($merchant->menus->pluck('category')->filter()->unique()->take(3) as $cat)
                                        <span class="badge badge-outline badge-sm">{{ $cat }}</span>
                                    @endforeach
                                    @if ($merchant->menus->count() > 0)
                                        <span class="badge badge-primary badge-sm">
                                            {{ $merchant->menus->count() }} menu
                                        </span>
                                    @endif
                                </div>
                                <div class="card-actions justify-between items-center mt-2">
                                    <span class="text-sm">
                                        mulai
                                        <b class="text-primary">{{ $merchant->menus->min('price') ? 'Rp'.number_format($merchant->menus->min('price'), 0, ',', '.') : '-' }}</b>
                                    </span>
                                    <a href="{{ route('orders.create', ['merchant' => $merchant->id]) }}" class="btn btn-primary btn-sm">
                                        Pesan Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $models->links('components/paginate') }}
                </div>
            @endif
        </div>
    </div>
@endsection
