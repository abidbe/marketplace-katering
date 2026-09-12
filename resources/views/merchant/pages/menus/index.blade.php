@extends('layouts.main')
@section('breadcrumb')
    <a href="{{ route('menus.index') }}" class="flex items-center gap-2">
        Kelola Menu
    </a>
@endsection
@php
    $indexRoute = fn() => route('menus.index');
    $createRoute = fn() => route('menus.create');
    $editRoute = fn($model) => route('menus.edit', $model->id);
    $deleteRoute = fn($model) => route('menus.destroy', $model->id);

    use App\Models\Menu;
    use App\Models\User;
@endphp
@section('pages')
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h1 class="card-title text-lg"><b>Daftar Menu</b></h1>
            </div>

            <form method="GET" action="{{ $indexRoute() }}" class="mb-4">
                <div class="flex flex-col sm:flex-row gap-2 w-full justify-between">
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <a href="{{ $createRoute() }}" onclick="modalFormAjax(this, event)"
                            class="btn btn-primary w-full sm:w-auto">
                            <i class="ri-add-line mr-2"></i>
                            Tambah Menu
                        </a>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        {{ $models->links('components/per-page') }}
                        <div class="form-control">
                            <select name="category" class="select select-bordered w-full" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control">
                            <select name="is_active" class="select select-bordered w-full" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                @foreach (User::IS_ACTIVE as $key => $label)
                                    <option value="{{ $key }}" {{ request('is_active') === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{ $models->links('components/search', get_defined_vars()) }}
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Aksi</th>
                            <th>Foto</th>
                            <x-sort-th column="name" label="Nama" />
                            <x-sort-th column="category" label="Kategori" />
                            <x-sort-th column="price" label="Harga" />
                            <x-sort-th column="is_active" label="Status" />
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($models as $index => $model)
                            <tr>
                                <td>{{ $models->firstItem() + $index }}</td>
                                <td>
                                    <a href="{{ $deleteRoute($model) }}" onclick="modalDeleteConfirm(this, event)"
                                        data-name="{{ $model->name }}" class="btn btn-ghost btn-sm">
                                        <i class="ri-delete-bin-line"></i>
                                    </a>
                                    <a href="{{ $editRoute($model) }}" onclick="modalFormAjax(this, event)"
                                        class="btn btn-ghost btn-sm">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                </td>
                                <td>
                                    @if ($model->file('foto')->hasFile())
                                        <div class="avatar">
                                            <div class="w-14 rounded">
                                                <img src="{{ $model->file('foto')->preview() }}" alt="{{ $model->name }}" />
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-base-content/40">-</span>
                                    @endif
                                </td>
                                <td>{{ $model->name }}</td>
                                <td>{{ $model->category ?? '-' }}</td>
                                <td>{{ $model->price_rp }}</td>
                                <td>
                                    <span class="badge {{ $model->is_active ? 'badge-success' : 'badge-error' }} badge-sm">
                                        {{ $model->is_active_val }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada menu. Klik "Tambah Menu" untuk memulai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-10">
                {{ $models->links('components/paginate') }}
            </div>
        </div>
    </div>
@endsection
