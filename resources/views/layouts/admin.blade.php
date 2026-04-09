<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'PSC Admin')</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-800">
        <div class="min-h-screen lg:flex">
            <aside class="w-full bg-teal-900 text-white lg:fixed lg:inset-y-0 lg:w-64">
                <div class="flex h-full flex-col justify-between px-6 py-8">
                    <div class="space-y-8">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-teal-100">Dashboard Admin</p>
                            <a href="{{ route('admin.products.index') }}" class="mt-2 block text-2xl font-bold">PSC Admin</a>
                        </div>

                        <nav class="space-y-2 text-sm">
                            <a
                                href="{{ route('admin.home') }}"
                                class="{{ request()->routeIs('admin.home') ? 'bg-teal-700 text-white' : 'text-teal-50 hover:bg-teal-800' }} flex items-center gap-3 rounded-2xl px-4 py-3 font-medium transition"
                            >
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">▦</span>
                                Dashboard
                            </a>
                            <a
                                href="{{ route('admin.products.index') }}"
                                class="{{ request()->routeIs('admin.products.*') ? 'bg-teal-700 text-white' : 'text-teal-50 hover:bg-teal-800' }} flex items-center gap-3 rounded-2xl px-4 py-3 font-medium transition"
                            >
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">□</span>
                                Produk
                            </a>
                        </nav>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold transition hover:bg-white/15">
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <div class="w-full lg:ml-64">
                <header class="border-b border-slate-200 bg-white/90 backdrop-blur">
                    <div class="flex flex-col gap-4 px-6 py-6 sm:flex-row sm:items-center sm:justify-between lg:px-10">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-teal-700">PSC Admin</p>
                            <h1 class="mt-1 text-2xl font-bold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-left shadow-sm transition hover:border-teal-200">
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-teal-100 font-semibold text-teal-800">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                </span>
                                <span>
                                    <span class="block text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                                    <span class="block text-xs text-slate-500">{{ auth()->user()->email }}</span>
                                </span>
                            </button>

                            <div
                                x-cloak
                                x-show="open"
                                @click.outside="open = false"
                                class="absolute right-0 z-20 mt-3 w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl"
                            >
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="px-6 py-8 lg:px-10">
                    @if (session('success'))
                        <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
