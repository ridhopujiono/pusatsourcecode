@extends('layouts.public')

@section('title', 'Keranjang | Pusat Source Code')
@section('meta_description', 'Tinjau produk di keranjang, lanjut checkout dengan Midtrans Snap, dan selesaikan pembelian source code digital Anda.')

@section('content')
    @php
        $user = auth()->user();
        $canCheckoutDirectly = $user && ! $user->is_admin && $user->hasVerifiedEmail() && $midtransConfigured;
    @endphp

    <section class="relative overflow-hidden border-b border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(45,212,191,0.18),_transparent_36%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-700">Keranjang Belanja</p>
                    <h1 class="mt-3 text-4xl font-extrabold text-slate-900">Checkout produk pilihan Anda</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                        Produk di keranjang akan diproses ke Midtrans Snap. Setelah pembayaran lunas, file source code dapat diunduh dari halaman pesanan Anda.
                    </p>
                </div>
            </div>

            @if ($products->isEmpty())
                <div class="psc-card mt-10 px-8 py-14 text-center">
                    <p class="text-2xl font-bold text-slate-900">Keranjang masih kosong</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Pilih produk dari katalog publik, lalu kembali ke halaman ini untuk lanjut checkout dan pembayaran.
                    </p>
                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ route('home') }}#katalog" class="psc-btn-primary">
                            Jelajahi Katalog
                        </a>
                        @auth
                            @if (! auth()->user()->is_admin)
                                <a href="{{ route('orders.index') }}" class="psc-btn-outline">
                                    Lihat Pesanan Saya
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            @else
                <div class="mt-10 grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                    <div class="space-y-4">
                        @foreach ($products as $product)
                            <article class="psc-card overflow-hidden p-5" data-cart-product-id="{{ $product->id }}">
                                <div class="flex flex-col gap-5 md:flex-row">
                                    <div class="md:w-56">
                                        @if ($product->list_thumbnail_url)
                                            <div class="overflow-hidden rounded-[1.5rem] border border-slate-200 bg-slate-100">
                                                <img src="{{ $product->list_thumbnail_url }}" alt="{{ $product->title }}" class="h-44 w-full object-cover">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <div>
                                                <span class="rounded-full border border-amber-200 bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                                    {{ $product->category }}
                                                </span>
                                                <h2 class="mt-3 text-2xl font-bold text-slate-900">{{ $product->title }}</h2>
                                            </div>
                                            <p class="text-2xl font-extrabold text-teal-800">{{ $product->price }}</p>
                                        </div>

                                        <p class="mt-4 text-sm leading-7 text-slate-600">
                                            {{ $product->description }}
                                        </p>

                                        <div class="mt-4 flex flex-wrap gap-2">
                                            @foreach ($product->tech_stack as $stack)
                                                <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                                    {{ $stack }}
                                                </span>
                                            @endforeach
                                        </div>

                                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                            <a href="{{ route('product.show', $product->slug) }}" class="psc-btn-outline">
                                                Lihat Detail
                                            </a>
                                            @if ($canCheckoutDirectly)
                                                <button
                                                    type="button"
                                                    class="psc-btn-primary checkout-action-button"
                                                    data-checkout-product-id="{{ $product->id }}"
                                                >
                                                    <span data-checkout-label>Checkout Produk Ini</span>
                                                </button>
                                            @endif
                                            <form method="POST" action="{{ route('cart.destroy', $product) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                                                    Hapus dari Keranjang
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach

                        <form method="POST" action="{{ route('cart.clear') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-red-200 hover:text-red-600">
                                Kosongkan Keranjang
                            </button>
                        </form>
                    </div>

                    <aside class="psc-card h-fit p-6 lg:sticky lg:top-8">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-teal-700">Ringkasan Checkout</p>
                        <div class="mt-6 space-y-4">
                            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4">
                                <span class="text-sm text-slate-600">Jumlah Produk</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $products->count() }} item</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4">
                                <span class="text-sm text-slate-600">Subtotal</span>
                                <span class="text-sm font-semibold text-slate-900">Rp {{ number_format($subtotalAmount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-base font-semibold text-slate-900">Total Bayar</span>
                                <span class="text-3xl font-extrabold text-teal-800">Rp {{ number_format($subtotalAmount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div id="checkout-status" class="mt-6 hidden rounded-2xl border px-4 py-3 text-sm font-medium"></div>

                        <div class="mt-6 space-y-4">
                            @guest
                                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm leading-7 text-amber-900">
                                    Login atau daftar dulu untuk melanjutkan checkout dan melihat halaman pesanan Anda.
                                </div>
                                <div class="flex flex-col gap-3">
                                    <a href="{{ route('public.login') }}" class="psc-btn-primary">
                                        Masuk
                                    </a>
                                    <a href="{{ route('public.register') }}" class="psc-btn-outline">
                                        Daftar Akun Baru
                                    </a>
                                </div>
                            @else
                                @if (auth()->user()->is_admin)
                                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm leading-7 text-red-700">
                                        Akun admin tidak digunakan untuk checkout publik. Gunakan akun user biasa untuk pembelian.
                                    </div>
                                @elseif (! auth()->user()->hasVerifiedEmail())
                                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm leading-7 text-amber-900">
                                        Verifikasi email Anda terlebih dahulu sebelum checkout agar pesanan dapat disimpan ke akun Anda.
                                    </div>
                                    <a href="{{ route('verification.notice') }}" class="psc-btn-primary w-full">
                                        Verifikasi Email
                                    </a>
                                @elseif (! $midtransConfigured)
                                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm leading-7 text-red-700">
                                        Midtrans belum dikonfigurasi di server. Isi `MIDTRANS_CLIENT_KEY` dan `MIDTRANS_SERVER_KEY` pada `.env`.
                                    </div>
                                @else
                                    <button type="button" id="checkout-all-button" class="psc-btn-primary checkout-action-button w-full">
                                        <span data-checkout-label>Checkout Semua Produk</span>
                                    </button>
                                    <p class="text-xs leading-6 text-slate-500">
                                        Gunakan checkout semua untuk memproses seluruh isi keranjang, atau checkout per produk langsung dari kartu item di sebelah kiri.
                                    </p>
                                @endif
                            @endguest
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    @if ($canCheckoutDirectly && $products->isNotEmpty())
        <script src="{{ $midtransSnapJsUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
        <script>
            const checkoutButtons = Array.from(document.querySelectorAll('.checkout-action-button'));
            const checkoutStatus = document.getElementById('checkout-status');
            const headerCartCount = document.getElementById('header-cart-count');

            function setCheckoutStatus(message, tone = 'success') {
                checkoutStatus.textContent = message;
                checkoutStatus.classList.remove('hidden', 'border-green-200', 'bg-green-50', 'text-green-800', 'border-red-200', 'bg-red-50', 'text-red-700', 'border-amber-200', 'bg-amber-50', 'text-amber-800');

                if (tone === 'success') {
                    checkoutStatus.classList.add('border-green-200', 'bg-green-50', 'text-green-800');
                } else if (tone === 'warning') {
                    checkoutStatus.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-800');
                } else {
                    checkoutStatus.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
                }
            }

            async function refreshOrder(url) {
                try {
                    await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                } catch (error) {
                    console.error(error);
                }
            }

            function setCheckoutButtonsState(disabled, activeButton = null) {
                checkoutButtons.forEach((button) => {
                    const label = button.querySelector('[data-checkout-label]');

                    if (!label) {
                        return;
                    }

                    if (!button.dataset.originalLabel) {
                        button.dataset.originalLabel = label.textContent.trim();
                    }

                    button.disabled = disabled;
                    label.textContent = button === activeButton && disabled
                        ? 'Menyiapkan Checkout...'
                        : button.dataset.originalLabel;
                });
            }

            async function startCheckout(activeButton, productIds = []) {
                setCheckoutButtonsState(true, activeButton);
                setCheckoutStatus('Membuat transaksi Midtrans untuk pesanan Anda...', 'warning');

                try {
                    const response = await fetch(@json(route('checkout.store')), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(productIds.length > 0 ? { product_ids: productIds } : {}),
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(payload.message || 'Checkout gagal diproses.');
                    }

                    if (headerCartCount && typeof payload.cart_count !== 'undefined') {
                        headerCartCount.textContent = payload.cart_count;
                    }

                    window.snap.pay(payload.snap_token, {
                        onSuccess: async function () {
                            await refreshOrder(payload.refresh_url);
                            window.location.href = payload.orders_url;
                        },
                        onPending: function () {
                            window.location.href = payload.orders_url;
                        },
                        onError: async function () {
                            await refreshOrder(payload.refresh_url);
                            window.location.href = payload.orders_url;
                        },
                        onClose: function () {
                            setCheckoutStatus('Popup pembayaran ditutup. Produk yang dipilih sudah dipindahkan ke pesanan Anda. Halaman keranjang akan dimuat ulang.', 'warning');

                            window.setTimeout(() => {
                                window.location.reload();
                            }, 1200);
                        },
                    });
                } catch (error) {
                    setCheckoutButtonsState(false);
                    setCheckoutStatus(error.message, 'error');
                }
            }

            document.getElementById('checkout-all-button')?.addEventListener('click', function () {
                startCheckout(this);
            });

            document.querySelectorAll('[data-checkout-product-id]').forEach((button) => {
                button.addEventListener('click', function () {
                    startCheckout(this, [Number(this.dataset.checkoutProductId)]);
                });
            });
        </script>
    @endif
@endpush
