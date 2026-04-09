@extends('layouts.public')

@section('title', 'Daftar | Pusat Source Code')
@section('meta_description', 'Buat akun Pusat Source Code dengan email dan password, lalu verifikasi email Anda.')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="mx-auto max-w-xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm lg:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-teal-700">Akun Publik</p>
            <h1 class="mt-3 text-3xl font-bold text-slate-900">Buat akun baru</h1>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                Setelah mendaftar, sistem akan mengirim email verifikasi ke alamat email Anda sebelum akun dipakai penuh.
            </p>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('public.register.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="psc-input" placeholder="Nama lengkap">
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="psc-input" placeholder="nama@email.com">
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="psc-input" placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="psc-input" placeholder="Ulangi password">
                </div>

                <button type="submit" class="psc-btn-primary w-full">
                    Daftar Sekarang
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">
                Sudah punya akun?
                <a href="{{ route('public.login') }}" class="font-semibold text-teal-700 transition hover:text-teal-800">Masuk di sini</a>
            </p>
        </div>
    </section>
@endsection
