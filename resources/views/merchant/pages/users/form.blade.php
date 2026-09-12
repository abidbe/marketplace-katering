@php
    if (empty($model->id)) {
        $formHref = fn() => route('users.store');
    } else {
        $formHref = fn($model) => route('users.update', $model->id);
    }

    use App\Models\User;
@endphp

<div class="p-6">
    <h2 class="text-xl font-bold mb-4">{{ empty($model->id) ? 'Tambah User' : 'Edit User' }}</h2>

    <form method="POST" action="{{ $formHref($model) }}" id="form-elem" class="space-y-3">
        @csrf
        @if (!empty($model->id))
            @method('PUT')
        @endif

        <div class="form-control">
            <input type="text" name="name" placeholder="Nama" class="input input-bordered w-full" />
        </div>

        <div class="form-control">
            <input type="email" name="email" placeholder="Email" class="input input-bordered w-full" />
        </div>

        <div class="form-control">
            <select name="role" class="select select-bordered w-full">
                <option value="">Pilih Role</option>
                @foreach (User::ROLE as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-control">
            <select name="is_active" class="select select-bordered w-full">
                @foreach (User::IS_ACTIVE as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-control">
            <input type="text" name="company_name" placeholder="Nama Perusahaan (khusus merchant)" class="input input-bordered w-full" />
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="form-control">
                <input type="text" name="city" placeholder="Kota" class="input input-bordered w-full" />
            </div>
            <div class="form-control">
                <input type="tel" name="phone" placeholder="No HP" class="input input-bordered w-full" />
            </div>
        </div>

        <div class="form-control">
            <textarea name="address" placeholder="Alamat" class="textarea textarea-bordered w-full"></textarea>
        </div>

        <div class="form-control">
            <textarea name="description" placeholder="Deskripsi" class="textarea textarea-bordered w-full"></textarea>
        </div>

        <div class="form-control">
            <input type="password" name="password" placeholder="Password" class="input input-bordered w-full" />
        </div>

        <div class="form-control">
            <input type="password" name="password_confirmation" placeholder="Ulangi Password" class="input input-bordered w-full" />
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
