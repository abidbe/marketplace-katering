@extends('layouts.main')
@section('breadcrumb')
    <a href="{{ route('caterings.index') }}" class="flex items-center gap-2">
        <i class="ri-arrow-left-line"></i> Cari Katering
    </a>
@endsection
@push('styles')
    <style>
        .menu-card {
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        .menu-card.menu-selected {
            outline-color: oklch(var(--p));
        }
    </style>
@endpush
@section('pages')
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h1 class="card-title text-lg mb-1"><b>Pesan dari {{ $merchant->company_name }}</b></h1>
            <p class="text-sm text-base-content/70 mb-4 flex flex-wrap items-center gap-x-3 gap-y-1">
                @if ($merchant->city)
                    <span><i class="ri-map-pin-line"></i> {{ $merchant->city }}</span>
                @endif
                @if ($merchant->phone)
                    <span><i class="ri-phone-line"></i> {{ $merchant->phone }}</span>
                @endif
            </p>
            @if ($merchant->description)
                <p class="text-sm text-base-content/70 mb-5 border border-base-300 rounded-box p-3">
                    {{ $merchant->description }}
                </p>
            @endif

            @if ($menus->isEmpty())
                <div class="text-center py-12 text-base-content/50">
                    <i class="ri-restaurant-off-line text-5xl mb-3 block"></i>
                    <p>Katering ini belum memiliki menu aktif.</p>
                    <a href="{{ route('caterings.index') }}" class="btn btn-primary btn-sm mt-4">Cari Katering Lain</a>
                </div>
            @else
                <form method="POST" action="{{ route('orders.store') }}" id="form-elem">
                    @csrf
                    <input type="hidden" name="merchant_id" value="{{ $merchant->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div class="form-control w-full">
                            <label class="label" for="delivery_date">
                                <span class="label-text font-semibold">Tanggal Kirim <span class="text-error">*</span></span>
                            </label>
                            <input type="date" id="delivery_date" name="delivery_date"
                                min="{{ now()->toDateString() }}" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control w-full">
                            <label class="label" for="note"><span class="label-text font-semibold">Catatan
                                    (opsional)</span></label>
                            <input type="text" id="note" name="note" maxlength="1000"
                                placeholder="cth: tanpa sambal, kirim sebelum jam 11" class="input input-bordered w-full" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" id="menuGrid">
                        @foreach ($menus as $menu)
                            <div class="card menu-card bg-base-200 overflow-hidden" data-price="{{ $menu->price }}">
                                <figure class="relative h-40 w-full bg-base-300">
                                    @if ($menu->file('foto')->hasFile())
                                        <img src="{{ $menu->file('foto')->preview() }}" alt="{{ $menu->name }}"
                                            class="h-full w-full object-cover" />
                                    @else
                                        <div class="h-full w-full flex items-center justify-center text-base-content/30">
                                            <i class="ri-image-line text-4xl"></i>
                                        </div>
                                    @endif
                                    <span
                                        class="qty-badge badge badge-primary absolute top-2 right-2 hidden font-bold">0</span>
                                </figure>
                                <div class="card-body p-4">
                                    <h3 class="font-bold leading-tight">{{ $menu->name }}</h3>
                                    @if ($menu->category)
                                        <span class="badge badge-outline badge-sm w-fit">{{ $menu->category }}</span>
                                    @endif
                                    <p class="text-sm line-clamp-2 min-h-[2.5rem] text-base-content/70">
                                        {{ $menu->description }}</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="font-bold text-primary">{{ $menu->price_rp }}</span>
                                        <div class="flex items-center gap-1">
                                            <button type="button" class="btn btn-circle btn-sm btn-outline btn-dec"
                                                aria-label="Kurangi">
                                                <i class="ri-subtract-line"></i>
                                            </button>
                                            <input type="hidden" name="items[{{ $loop->index }}][menu_id]"
                                                value="{{ $menu->id }}" disabled>
                                            <input type="number" name="items[{{ $loop->index }}][portions]" value="0"
                                                min="0" max="10000" disabled aria-label="Jumlah porsi"
                                                class="qty-input input input-bordered input-sm w-14 text-center" />
                                            <button type="button" class="btn btn-circle btn-sm btn-primary btn-inc"
                                                aria-label="Tambah">
                                                <i class="ri-add-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="divider"></div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-sm text-base-content/70" id="summary"></p>
                        <div class="flex gap-2">
                            <a href="{{ route('caterings.index') }}" class="btn btn-ghost">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-send-plane-line mr-1"></i> Buat Pesanan
                            </button>
                        </div>
                    </div>
                </form>

                @push('scripts')
                    <script type="module">
                        const grid = document.querySelector('#menuGrid');
                        const rp = n => 'Rp' + n.toLocaleString('id-ID');

                        function sync() {
                            let count = 0,
                                portions = 0,
                                grand = 0;
                            grid.querySelectorAll('.menu-card').forEach(card => {
                                const [menuField, qtyField] = card.querySelectorAll('input');
                                const qty = parseInt(qtyField.value) || 0;
                                menuField.disabled = qtyField.disabled = qty <= 0;
                                const badge = card.querySelector('.qty-badge');
                                badge.classList.toggle('hidden', qty <= 0);
                                badge.textContent = qty;
                                card.classList.toggle('menu-selected', qty > 0);
                                if (qty > 0) {
                                    count++;
                                    portions += qty;
                                    grand += qty * parseInt(card.dataset.price);
                                }
                            });
                            document.getElementById('summary').innerHTML = count ?
                                '<b>' + count + ' menu</b> • ' + portions + ' porsi • Total <b class="text-primary">' +
                                rp(grand) + '</b>' :
                                'Belum ada menu dipilih — klik <b>+</b> pada menu.';
                        }

                        grid.addEventListener('click', e => {
                            const btn = e.target.closest('.btn-inc, .btn-dec');
                            if (!btn) return;
                            const input = btn.closest('.menu-card').querySelector('.qty-input');
                            input.disabled = false;
                            input.value = Math.max(0, Math.min(10000,
                                (parseInt(input.value) || 0) + (btn.classList.contains('btn-inc') ? 1 : -1)));
                            sync();
                        });

                        grid.addEventListener('input', e => {
                            if (!e.target.matches('.qty-input')) return;
                            e.target.value = Math.max(0, Math.min(10000, parseInt(e.target.value) || 0));
                            sync();
                        });

                        $('#form-elem').on('submit', function (e) {
                            e.preventDefault();
                            const form = this;
                            if (!grid.querySelector('.qty-input:not([disabled])')) {
                                Swal.fire('Belum ada menu dipilih', 'Klik + pada menu yang ingin dipesan.', 'warning');
                                return;
                            }
                            $.post(form.action, $(form).serialize())
                                .done(() => form.submit())
                                .fail(xhr => Swal.fire('Pesanan belum sesuai',
                                    xhr.responseJSON?.message ?? 'Terjadi kesalahan, coba lagi.', 'error'));
                        });

                        sync();
                    </script>
                @endpush
            @endif
        </div>
    </div>
@endsection
