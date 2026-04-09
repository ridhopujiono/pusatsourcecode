<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-teal-700">PSC Admin</p>
            <h2 class="mt-2 text-3xl font-bold text-slate-900">Masuk ke dashboard</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                Gunakan akun admin untuk mengelola katalog, status produk, dan urutan tampilan di website publik.
            </p>
        </div>

        <x-auth-session-status class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" :status="session('status')" />

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                <input
                    id="email"
                    class="psc-input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@pusatsourcecode.site"
                >
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

                <input
                    id="password"
                    class="psc-input"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                >
            </div>

            <label for="remember_me" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-500" name="remember">
                Ingat sesi login di browser ini
            </label>

            <button type="submit" class="psc-btn-primary w-full">
                Log In Admin
            </button>
        </form>
    </div>
</x-guest-layout>
