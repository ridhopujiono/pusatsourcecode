@extends('layouts.public')

@section('title', 'Masuk | Pusat Source Code')
@section('meta_description', 'Masuk ke akun Pusat Source Code dengan email dan password.')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="mx-auto max-w-xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm lg:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-teal-700">Akun Publik</p>
            <h1 class="mt-3 text-3xl font-bold text-slate-900">Masuk ke akun Anda</h1>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                Gunakan email dan password yang sudah terdaftar. Jika email belum diverifikasi, Anda akan diminta menyelesaikan verifikasi terlebih dahulu.
            </p>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('public.login.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="psc-input" placeholder="nama@email.com">
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-4">
                        <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-teal-700 transition hover:text-teal-800">
                                Lupa password?
                            </a>
                        @endif
                    </div>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="psc-input" placeholder="••••••••">
                </div>

                <label for="remember" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-500">
                    Ingat sesi login saya
                </label>

                <button type="submit" class="psc-btn-primary w-full">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">
                Belum punya akun?
                <a href="{{ route('public.register') }}" class="font-semibold text-teal-700 transition hover:text-teal-800">Daftar di sini</a>
            </p>
        </div>
    </section>
@endsection
