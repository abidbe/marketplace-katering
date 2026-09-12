@extends('layouts.main')
@section('breadcrumb')
    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2">
        Profil Katering
    </a>
@endsection
@php
    $action = route('profile.update');
@endphp
@section('pages')
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h1 class="card-title text-lg"><b>Profil Katering</b></h1>
            <p class="text-sm text-base-content/60 mb-4">Isi data perusahaan katering Anda agar mudah ditemukan kantor.</p>

            <form method="POST" action="{{ $action }}" class="space-y-3 max-w-2xl" id="form-elem">
                @csrf
                @method('patch')

                <div class="form-control">
                    <label class="label"><span class="label-text">Nama Perusahaan</span></label>
                    <input type="text" name="company_name" value="{{ old('company_name', $model->company_name) }}"
                        placeholder="PT. Katering Sejahtera"
                        class="input input-bordered w-full @error('company_name') input-error @enderror" />
                    @error('company_name')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Kota</span></label>
                    <input type="text" name="city" value="{{ old('city', $model->city) }}" placeholder="Jakarta"
                        class="input input-bordered w-full @error('city') input-error @enderror" />
                    @error('city')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">No Telepon</span></label>
                    <input type="tel" name="phone" value="{{ old('phone', $model->phone) }}" placeholder="0812xxxxxxxx"
                        class="input input-bordered w-full @error('phone') input-error @enderror" />
                    @error('phone')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Alamat</span></label>
                    <textarea name="address" placeholder="Alamat lengkap usaha"
                        class="textarea textarea-bordered w-full @error('address') input-error @enderror">{{ old('address', $model->address) }}</textarea>
                    @error('address')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Deskripsi</span></label>
                    <textarea name="description" placeholder="Ceritakan tentang katering Anda..."
                        class="textarea textarea-bordered w-full @error('description') input-error @enderror">{{ old('description', $model->description) }}</textarea>
                    @error('description')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan Profil</button>
            </form>
        </div>
    </div>
@endsection
