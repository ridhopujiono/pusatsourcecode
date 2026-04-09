@extends('layouts.public')

@section('title', 'Verifikasi Email | Pusat Source Code')
@section('meta_description', 'Verifikasi email akun Pusat Source Code Anda.')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="mx-auto max-w-xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm lg:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-teal-700">Verifikasi Email</p>
            <h1 class="mt-3 text-3xl font-bold text-slate-900">Periksa inbox email Anda</h1>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                Sebelum melanjutkan, klik link verifikasi yang sudah dikirim ke email Anda. Jika belum menerima email, kirim ulang dari tombol di bawah ini.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="mt-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    Link verifikasi baru sudah dikirim ke email Anda.
                </div>
            @endif

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="psc-btn-primary">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="psc-btn-outline">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
