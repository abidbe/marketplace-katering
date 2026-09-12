@php
    $action = $model->id ? route('menus.update', $model->id) : route('menus.store');
@endphp

<div class="p-6">
    <h2 class="text-xl font-bold mb-4">{{ empty($model->id) ? 'Tambah Menu' : 'Edit Menu' }}</h2>

    <form method="POST" action="{{ $action }}" id="form-elem" class="space-y-3" enctype="multipart/form-data">
        @csrf

        @if ($model->id)
            @method('PUT')
        @endif

        <div class="form-control">
            <input type="text" name="name" placeholder="Nama Menu" class="input input-bordered w-full" />
        </div>

        <div class="form-control">
            <input type="text" name="category" placeholder="Kategori / jenis makanan (mis: Nasi Kotak)"
                list="category-list" class="input input-bordered w-full" />
            <datalist id="category-list">
                @foreach (App\Models\Menu::distinctCategories() as $cat)
                    <option value="{{ $cat }}"></option>
                @endforeach
            </datalist>
        </div>

        <div class="form-control">
            <textarea name="description" placeholder="Deskripsi" class="textarea textarea-bordered w-full"></textarea>
        </div>

        <div class="form-control">
            <input type="number" name="price" placeholder="Harga (Rp, mis: 25000)" min="0"
                class="input input-bordered w-full" />
        </div>

        <div class="form-control">
            <select name="is_active" class="select select-bordered w-full">
                @foreach (App\Models\User::IS_ACTIVE as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Foto Menu</span></label>
            <input type="file" name="upload_foto" accept="image/jpeg,image/png,image/webp"
                class="file-input file-input-bordered w-full" />
            <label class="label"><span class="label-text-alt">jpg/png/webp, maks 2MB</span></label>
            @if ($model->file('foto')->hasFile())
                <div class="mt-2">
                    <p class="text-sm mb-2 font-bold">Foto Saat Ini:</p>
                    <div class="avatar">
                        <div class="w-24 rounded">
                            <img src="{{ $model->file('foto')->preview() }}" alt="Preview" />
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-full">
            {{ empty($model->id) ? 'Simpan' : 'Update' }}
        </button>
    </form>
</div>
<script id="data-json" type="application/json">
    {!! $model->toJson(JSON_FORCE_OBJECT) !!}
</script>

<script type="module">
    jsonScriptToFormFields('#form-elem', '#data-json');
    $('#form-elem').formAjaxSubmit();
</script>
