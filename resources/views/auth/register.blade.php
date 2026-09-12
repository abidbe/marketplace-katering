@extends('auth.layout')

@section('title', 'Register')

@section('content')
<div class="flex items-center justify-center px-4 py-12">
    <div class="card bg-base-100 shadow-xl w-full max-w-md">
        <div class="card-body gap-4">
            <h1 class="card-title text-2xl justify-center">Register</h1>
            <p class="text-center text-sm text-base-content/60">Buat akun baru untuk memulai.</p>

            <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
                @csrf

                <div class="form-control w-full">
                    <label for="name" class="label"><span class="label-text">Nama</span></label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Nama lengkap"
                        class="input input-bordered w-full @error('name') input-error @enderror"
                    />
                    @error('name')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label for="email" class="label"><span class="label-text">Email</span></label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        placeholder="email@example.com"
                        class="input input-bordered w-full @error('email') input-error @enderror"
                    />
                    @error('email')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label for="password" class="label"><span class="label-text">Password</span></label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                        class="input input-bordered w-full @error('password') input-error @enderror"
                    />
                    @error('password')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label for="password_confirmation" class="label"><span class="label-text">Konfirmasi Password</span></label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi password"
                        class="input input-bordered w-full @error('password') input-error @enderror"
                    />
                </div>

                <button type="submit" class="btn btn-primary w-full">Register</button>
            </form>

            <p class="text-center text-sm text-base-content/60">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="link link-primary">Log in</a>
            </p>
        </div>
    </div>
</div>
@endsection
