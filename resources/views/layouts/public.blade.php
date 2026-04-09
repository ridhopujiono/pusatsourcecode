<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Pusat Source Code')</title>
        <meta name="description" content="@yield('meta_description', 'Marketplace source code digital siap deploy untuk bisnis, startup, dan UMKM di Indonesia.')">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-800">
        @php
            $publicUser = auth()->user();
        @endphp

        <div class="min-h-screen">
            <header class="relative z-20 border-b border-slate-200/80 bg-white/70 backdrop-blur">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-4 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-teal-700 text-sm font-extrabold text-white">
                                PSC
                            </span>
                            <span>
                                <span class="block text-sm font-semibold uppercase tracking-[0.2em] text-teal-700">Pusat Source Code</span>
                                <span class="block text-sm text-slate-500">Marketplace source code siap deploy</span>
                            </span>
                        </a>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 lg:justify-end">
                        <a href="{{ route('home') }}#katalog" class="rounded-2xl border border-transparent px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-200 hover:bg-white">
                            Katalog
                        </a>

                        <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-teal-200 hover:text-teal-700">
                            Keranjang
                            <span id="header-cart-count" class="inline-flex min-w-6 items-center justify-center rounded-full bg-teal-700 px-2 py-0.5 text-xs font-bold text-white">
                                {{ $cartItemCount ?? 0 }}
                            </span>
                        </a>

                        @auth
                            @if (! $publicUser?->is_admin)
                                <a href="{{ route('orders.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-teal-200 hover:text-teal-700">
                                    Pesanan Saya
                                </a>
                            @endif

                            <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 text-sm font-bold text-teal-800">
                                    {{ strtoupper(substr($publicUser?->name ?? 'U', 0, 1)) }}
                                </span>

                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $publicUser?->name }}</p>
                                    <p class="hidden truncate text-xs text-slate-500 sm:block">{{ $publicUser?->email }}</p>
                                </div>
                            </div>

                            @if (! $publicUser?->is_admin && ! $publicUser?->hasVerifiedEmail())
                                <a href="{{ route('verification.notice') }}" class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-800 transition hover:bg-amber-100">
                                    Verifikasi Email
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-2xl border border-slate-300 bg-transparent px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-red-200 hover:text-red-600">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('public.login') }}" class="rounded-2xl border border-slate-300 bg-transparent px-5 py-4 text-sm font-semibold text-slate-800 transition hover:border-teal-300 hover:text-teal-700">
                                Masuk
                            </a>
                            <a href="{{ route('public.register') }}" class="rounded-2xl bg-teal-700 px-5 py-4 text-sm font-semibold text-white transition hover:bg-teal-800">
                                Daftar
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            @if (session('success') || session('error'))
                <div class="relative z-10 mx-auto max-w-7xl px-6 py-4 lg:px-8">
                    @if (session('success'))
                        <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')

            <footer class="border-t border-slate-200 bg-white">
                <div class="mx-auto grid max-w-7xl gap-10 px-6 py-14 md:grid-cols-3 lg:px-8">
                    <div class="space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-700">Pusat Source Code</p>
                        <h2 class="text-2xl font-bold text-slate-900">Marketplace source code siap deploy untuk bisnis Indonesia.</h2>
                        <p class="text-sm leading-7 text-slate-600">
                            Solusi digital yang dikurasi untuk retail, e-commerce, klinik, edukasi, dan berbagai kebutuhan operasional modern.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-700">Kontak</p>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li>Email: hello@pusatsourcecode.site</li>
                            <li>WhatsApp: +62 822-5780-2227</li>
                            <li>Alamat: Jakarta Selatan, Indonesia</li>
                        </ul>
                    </div>

                    <div class="space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-700">Legal</p>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li><a href="{{ route('legal') }}" class="transition hover:text-teal-700">Syarat & Ketentuan</a></li>
                            <li><a href="{{ route('legal') }}#kebijakan-refund" class="transition hover:text-teal-700">Kebijakan Refund</a></li>
                            <li><a href="{{ route('home') }}#katalog" class="transition hover:text-teal-700">Katalog Produk</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-slate-200 px-6 py-6 text-center text-sm text-slate-500 lg:px-8">
                    &copy; {{ now()->year }} Pusat Source Code. Seluruh hak cipta dilindungi.
                </div>
            </footer>
        </div>

        @stack('scripts')
    </body>
</html>
