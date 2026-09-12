@extends('auth.layout')

@section('title', 'Log in')

@section('content')
<div class="flex items-center justify-center px-4 py-12">
    <div class="card bg-base-100 shadow-xl w-full max-w-md">
        <div class="card-body gap-4">
            <h1 class="card-title text-2xl justify-center">Log in</h1>
            <p class="text-center text-sm text-base-content/60">Selamat datang kembali! Silakan log in untuk melanjutkan.</p>

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf

                <div class="form-control w-full">
                    <label for="email" class="label"><span class="label-text">Email</span></label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
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
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="input input-bordered w-full @error('password') input-error @enderror"
                    />
                    @error('password')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="remember" class="checkbox checkbox-sm" />
                    <span class="label-text">Ingat saya</span>
                </label>

                <button type="submit" class="btn btn-primary w-full">Log in</button>
            </form>

            <p class="text-center text-sm text-base-content/60">
                Belum punya akun?
                <a href="{{ route('register') }}" class="link link-primary">Register</a>
            </p>
        </div>
    </div>
</div>
@endsection
