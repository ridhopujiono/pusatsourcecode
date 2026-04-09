<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Pusat Source Code') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 text-slate-900 antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(45,212,191,0.35),_transparent_32%),radial-gradient(circle_at_bottom_right,_rgba(251,191,36,0.28),_transparent_28%),linear-gradient(135deg,#020617,#0f172a_55%,#0f766e_130%)]"></div>

            <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-10 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                    <div class="text-white">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur">
                            Katalog Source Code Siap Deploy
                        </a>
                        <h1 class="mt-6 max-w-xl text-4xl font-extrabold leading-tight sm:text-5xl">
                            Login admin untuk mengelola katalog Pusat Source Code.
                        </h1>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-slate-200">
                            Panel ini digunakan untuk mengelola produk digital, mengatur status tayang, dan memperbarui urutan katalog yang tampil di website publik.
                        </p>

                        <div class="mt-8 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                                <p class="text-2xl font-bold">6+</p>
                                <p class="mt-2 text-sm text-slate-200">Kategori source code siap jual</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                                <p class="text-2xl font-bold">CRUD</p>
                                <p class="mt-2 text-sm text-slate-200">Kelola produk, status aktif, dan urutan katalog</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                                <p class="text-2xl font-bold">WhatsApp</p>
                                <p class="mt-2 text-sm text-slate-200">Landing publik langsung terhubung ke konsultasi</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-white/10 bg-white p-6 shadow-2xl shadow-black/30 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
